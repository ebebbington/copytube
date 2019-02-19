<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 16:39
 */

if (isset($_COOKIE['id'])) {
    $serverName = "localhost";
    $username = "root";
    $password = "password";
    $id = $_COOKIE['id'];
//create connection to db
    $connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
    if ($connection->connect_error) {
        die("connection failed: " . $connection->connect_error);
    }
    $sql = "UPDATE users SET loggedIn = 1 WHERE id = '$id'";
    $connection->query($sql);
    $connection->close();
    setcookie("PHPSESSID", "", time() - 3600, '/');
    setcookie("username", "", time() - 3600, '/');
    setcookie("name", "", time() - 3600, '/');
    setcookie("id", "", time() - 3600, '/');
    session_abort();
    session_unset();
    echo "<script>window.location.replace('http://localhost/copytube/login/login.html')";
} else {
    echo "<script>window.location.replace('http://localhost/copytube/login/login.html')";
}
