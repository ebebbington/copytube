<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 16:39
 */

if (isset($_COOKIE['usernameId'])) {
    $id = $_COOKIE['usernameId'];
    $serverName = "localhost";
    $username = "root";
    $password = "password";
//create connection to db
    $connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
    if ($connection->connect_error) {
        die("connection failed: " . $connection->connect_error);
    }
    $sql = "SELECT users_username_id FROM sessions WHERE username_id = '$id'";
    $result = $connection->query($sql);
    $response = $result->fetch_all(MYSQLI_ASSOC);
    $id = $response[0]['users_username_id'];
    $sql = "UPDATE users SET loggedIn = 1 WHERE id = '$id'";
    $connection->query($sql);
    $sql = "DELETE FROM sessions WHERE users_username_id = '$id'";
    $connection->query($sql);
    $connection->close();
    setcookie("sessionId", "", time() - 3600, '/');
    setcookie('PHPSESSID', '', time()-3600, '/');
    setcookie("usernameId", "", time() - 3600, '/');
    setcookie("name", "", time() - 3600, '/');
    session_abort();
    session_unset();
    echo "<script>window.location.replace('http://localhost/copytube/login/login.html')";
} else {
    echo "<script>window.location.replace('http://localhost/copytube/login/login.html')";
}
