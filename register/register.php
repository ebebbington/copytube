<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 12:12
 */

$serverName = "localhost";
$username = "root";
$password = "password";
$usernameInput = $_POST['username'];
$passwordInput = $_POST['password'];
// Validate
// todo :: validate server side here
// Hash
$hash = password_hash($passwordInput, PASSWORD_BCRYPT);
//create connection
$connection = new mysqli($servername, $username, $password, 'copytube');
//check connection
if ($connection->connect_error) {
    die("connection to database inside savecomment.php has failed: " + $connection->connect_error);
}
//if connection works, set variable to string of inserting data
$sql = "INSERT INTO users (username, password, loggedIn) VALUES ($usernameInput, $hash, 1)";
//set this data in the database
$connection->query($sql);
$connection->close();