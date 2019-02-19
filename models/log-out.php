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
$user = $_COOKIE['username'];
//create connection to db
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
if ($connection->connect_error) {
    die("connection failed: " . $connection->connect_error);
}
$sql = "UPDATE users SET loggedIn = 1 WHERE username = '$user'";
$connection->query($sql);
$connection->close();
setcookie("PHPSESSID", "", time() - 3600, '/');
setcookie("username", "", time() - 3600, '/');
setcookie("name", "", time() - 3600, '/');
session_abort();
print_r(json_encode(true));
