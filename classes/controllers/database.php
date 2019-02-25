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
    public $connection;

    //
    // Initialise
    //
    public function __construct() {
        $this->servername = "localhost";
        $this->username = "root";
        $this->password = "password";
        $this->databaseName = "copytube";
    }

    //
    // Create DB connection
    //
    public function openDatabaseConnection () {
        try {
            $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->databaseName);
        } catch (exception $error) {
            die("Connection to database has failed");
        }
    }

    //
    // Close DB connection
    //
    public function closeDatabaseConnection () {
        try {
            $this->connection->close;
        } catch (exception $error) {
            $this->closeDatabaseConnection();
        }
    }
}