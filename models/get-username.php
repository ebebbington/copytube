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
$sql = "SELECT username, loggedIn FROM users WHERE loggedIn = 0";
//$result = query of $sql
$result = $connection->query($sql);
// returns false is no user is logged in
if ($result == false){
    die ('broke');
} else {
    // check if that user is logged in to isplay error
    $response = $result->fetch_all(MYSQLI_ASSOC);
    if (count($response) === 0) {
        print_r('false');
        $connection->close();
        return;
    } else {
        print_r(true);
        $connection->close();
    }
}