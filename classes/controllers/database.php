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

    public function __construct() {
        $this->servername = "localhost";
        $this->username = "root";
        $this->password = "password";
        $this->databaseName = "copytube";
    }

    public function connectToDatabase () {
        $this->connection = new mysqli($this->servername, $this->username, $this->password, $this->databaseName);
    }

    public function closeDatabaseConnection () {
        $this->connection->close;
    }
}