<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 12:05
 */

 /*
 Sample DB query
            $query = "SELECT * FROM users";
            $this->connection = new mysqli($this->server, $this->username, $this->password, $this->database);
            $result = mysqli_query($this->connection, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    var_dump($row);
                }
            }
*/

/**
 * Notes on Databas
 * 
 * 1. SQL statements should be constructed like:
 *      $sql = "SELECT * FROM users WHERE username = ?";
 *      or
 *      $sql = "SELECT * FROM users WHERE username = 'Edward'";
 * 
 * 2. Creating the database connection the old way
 *      $db;
 *      $db->server = $config['Database']['server'];
 *      $db->username = $config['Database']['username'];
 *      $db->password = $config['Database']['password'];
 *      $db->database = $config['Database']['database'];
 *      $db->connection = new mysqli($db->server, $db->username, $db->password, $db->database);
 *      $query = $db->connection->prepare(self::ADD_NEW_USER);
 *      $query->bind_param('sssii', $username, $email, $hash, $loggedIn, $loginAttempts);
 *      $query->execute();
 *      $result = $query->get_result()->fetch_all(MYSQLI_ASSOC);
 * 
 * 3. Creating a database connection the new way
 *      $this->server = $config['Database']['server'];
 *      $this->username = $config['Database']['username'];
 *      $this->password = $config['Database']['password'];
 *      $this->database = $config['Database']['database'];
 *      $this->options = [
 *          PDO::ATTR_EMULATE_PREPARES => false,
 *          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 *          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
 *      ];
 *      $this->dsn = "mysql:host=$this->server;dbname=$this->database;charset=utf8mb4";
 *      $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
 *      $query = $this->pdo->prepare($sql);
 *      $query->execute($data);
 *      $result = $query->fetchAll(PDO::FETCH_ASSOC);
 * 
 * 4. When instatiating the Database class
 *      $db = new Database();
 *      $result = $db->runQuery(self::GET_USER_BY_EMAIL, [$email]);
 *      if ($result['success'] === false) {
 *          return $result; // this holds the errors
 *      }
 *      // now do your own thing with the data, rowcount here would be set
 */

/**
 *  The Database Model
 * 
 * Creates the database connection and will run any query if the SQL and data is passed
 * in. The fetch is assigned to the $row var which can be checked to see if
 * any rows were affected
 * 
 * @author Edward Bebbington
 * @copyright
 * @license
 * @method __construct()
 * @method runQuery() The function thats called when any query needs to run
 */
class DatabaseModel
{
    // ///////////////////////////////////////////////////////
    // Class Properties
    /////////////////////////////////////////////////////////
    /** @var String $server The name of the SQL Docker container */
    private $server = '';
    /** @var String $username Username to log in to the database in the connection stage */
    private $username = '';
    /** @var String $password Password to go with the username ion login to database */
    private $password = '';
    /** @var String $database The name of the database to use */
    private $database = '';
    /** @var mysqli $connection */
    private $connection;
    /** @var ? $pdo Allows the prep of prepared statements */
    private $pdo;
    /** @var String $dsn I honestly am not sure what this means */
    private $dsn;
    /** @var Array $options The array holding the options for PDO */
    private $options;
    /** @var Array $row If a row was affected by a query, this will be set */
    public $row = [];

    /**
     * Constructor
     * 
     * Create the properties and create the database connection and PDO class
     */
    public function __construct() {
        try {
            // Set database credentials
            $configPath = $_SERVER['DOCUMENT_ROOT'] . '/copytube.ini';
            $config = parse_ini_file($configPath, true);
            $this->server = $config['Database']['server'];
            $this->username = $config['Database']['username'];
            $this->password = $config['Database']['password'];
            $this->database = $config['Database']['database'];

            // Pepare options for PDO
            $this->options = [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];

            // Prepare dsn for PDO
            $this->dsn = "mysql:host=$this->server;dbname=$this->database;charset=utf8mb4";

            // Set pdo
            $this->pdo = new PDO($this->dsn, $this->username, $this->password, $this->options);
        } catch (Exception $e) {
            trigger_error('Error on trying to set up the database class: ' . $e->getMessage());
        }
    }

    /**
     * Run any query
     * 
     * Used to run any query so there is a single function that does this instead
     * of these calls being all over the place.
     * This function came around after researching PDO, which has better implementations of database actions
     * compared to my own.
     * 
     * The end goal will be modifying the $row property. This will be set if a row was affected
     * 
     * @param string $sql The SQL query e.g. SELECT * FROM users
     * @param array $data An array of values to pass into prepared statements. Leave empty if not a prepared SQL
     */
    public function runQuery (String $sql = '', Array $data = []) {
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute($data); // Not a problem if there isnt any
            // Grab the results
            $this->row = $query->fetchAll(PDO::FETCH_ASSOC);
            // If row isnt set e.g using an update statement, then assign the row count
            if (!$this->row) {
                $this->row = $query->rowCount();
                if (!$this->row) {
                    // we got a problem
                    trigger_error("The database wasnt affected at all when running the query $sql with the data: " . $data);
                } 
            }
            // Destroy the object to ensure we close the connection. PHP will do this when the script ends but for best measures
            $query = null;
        } catch (Exception $e) {
            trigger_error('Error when executing the runQuery function in the database class: ' . $e->getMessage());
        }
    }
}