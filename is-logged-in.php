<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 16:16
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
//Get username of logged in from db
$sql = "SELECT loggedIn FROM `users` WHERE loggedIn='0'";
//$result = query of $sql
$result = $connection->query($sql);
//If query fails, die. If not then get results
if ($result == false){
    die("PHP Response SQL: The query of sql is false.
        Result is: $result.
        Sql is: $sql.");
} else {
    $response = $result->fetch_all(MYSQLI_ASSOC);
    print_r(json_encode($response));
    $connection->close();
}