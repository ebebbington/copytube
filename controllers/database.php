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
 *      $result = $db->runQuery($sql, $data);
 * Easy, the database class handles everything else
 */

 /**
  * @author Edward Bebbington
  */
class Database
{
    private $server;
    private $username;
    private $password;
    private $database;
    /** @var mysqli $connection */
    public $connection;

    private $pdo;
    private $dsn;
    private $options;

    //
    // Initialise
    //
    public function __construct() {
        try {
            // Set database credentials
            $configPath = $_SERVER['DOCUMENT_ROOT'] . '/config/copytube.ini';
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
            // todo :: log and handle
            throw new Exception($e);
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
     * @param string $sql The SQL query e.g. SELECT * FROM users
     * @param array $data An array of values to pass into prepared statements. Leave empty if not a prepared SQL
     * @return array $result 
     *      int rowCount containing rowCount for EDITing values
     *      array data data from SELECTing => [0]['username], [1]['username']
     *      bool success If the query succeeded
     *      string message The message to be passed back with the success property
     */
    public function runQuery ($sql = '', $data = []) {
        // The database already makes a connection below, no need to specifically open it
        $result = [
            'rowCount' => 0,
            'data' => [],
            'success' => false,
            'message' => 'There was a problem with executing the database action'
        ];
        try {
            $query = $this->pdo->prepare($sql);
            $query->execute($data); // Not a problem if there isnt any
            // Grab the results
            $result['rowCount'] = $query->rowCount();
            $result['data'] = $query->fetchAll(PDO::FETCH_ASSOC);
            $result['success'] = true;
            $result['message'] = 'Successfully executed the database query';
            // Destroy the object to ensure we close the connection. PHP will do this when the script ends but for best measures
            $query = null;
        } catch (Exception $e) {
            // todo :: log
        }
        return $result;
    }
}