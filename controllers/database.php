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
    private $databaseName;
    /** @var mysqli $connection */
    public $connection;
    private $isConnected;

    //
    // Initialise
    //
    public function __construct() {
        $this->servername = "192.168.56.101";
        $this->username = "root";
        $this->password = "JahRastafarI1298";
        $this->databaseName = "copytube";
        $this->isConnected = false;
    }

    //
    // Create DB connection
    //
    public function openDatabaseConnection () {
        // Failsafe
        if ($this->isConnected === false) {
            $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->databaseName);
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