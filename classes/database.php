<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 12:05
 */

class Database
{
    const GET_VIDEOS = "SELECT title, src, description, poster, width, height FROM videos";

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


}