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
class Database
{
    private $servername;
    private $username;
    private $password;
    private $database;
    /** @var mysqli $connection */
    public $connection;
    private $isConnected;

    //
    // Initialise
    //
    public function __construct() {
        $configPath = $_SERVER['DOCUMENT_ROOT']. '/config/copytube.ini';
        $config = parse_ini_file($configPath, true);
        $this->server = $config['Database']['server'];
        $this->username = $config['Database']['username'];
        $this->password = $config['Database']['password'];
        $this->database = $config['Database']['database'];
    }

    //
    // Create DB connection
    //
    public function openDatabaseConnection () {
        // Failsafe
        if ($this->isConnected === false) {
            try {
                $this->connection = new mysqli($this->server, $this->username, $this->password, $this->database);
                $this->isConnected = true;
            } catch (Exception $e) {
                $this->isConnected = false;
                var_dump('error when trying to create a connection to db', $e);
            }
        } else {
            $this->connection = new mysqli($this->server, $this->username, $this->password, $this->database);
        }
    }

    //
    // Close DB connection
    //
    public function closeDatabaseConnection () {
        // Failsafe
        try {
            $this->connection->close();
            $this->isConnected = false;
        } catch (Exception $e) {}
    }
}