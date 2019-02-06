<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 16:39
 */

$serverName = "localhost";
$username = "root";
$password = "password";
//create connection to db
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
if ($connection->connect_error) {
    die("connection failed: " . $connection->connect_error);
}
$sql = "UPDATE users SET loggedIn=1 WHERE loggedIn=0";
$connection->query($sql);
$connection->close();
