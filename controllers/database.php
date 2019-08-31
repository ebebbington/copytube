<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 12:05
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
        $this->server = $config['Database']['servername'];
        $this->username = $config['Database']['username'];
        $this->password = $config['Database']['password'];
        $this->database = $config['Database']['database'];
        $this->isConnected = false;
    }

    //
    // Create DB connection
    //
    public function openDatabaseConnection () {
        // Failsafe
        if ($this->isConnected === false) {
            $this->connection = new mysqli($this->server, $this->username, $this->password, $this->database);
            $this->isConnected = true;
        }
    }

    //
    // Close DB connection
    //
    public function closeDatabaseConnection () {
        // Failsafe
        if ($this->isConnected === true) {
            $this->connection->close();
            $this->isConnected = false;
        }
    }
}