<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 13:03
 */

$serverName = "localhost";
$username = "root";
$password = "password";
$usernameInput = $_GET['username'];
$passwordInput = $_GET['password'];

// Validation
if ($usernameInput === '' || $usernameInput >= ($maxLength + 1) || trim($usernameInput) === 0 || $usernameInput === null) {
    print_r(false);
} else {
    if ($passwordInput === '' || $passwordInput >= ($maxLength + 1) || trim($passwordInput) === 0
      || $passwordInput === null
    ) {
        print_r(false);
    } else {
        //create connection to db
        $connection = new mysqli($serverName, $username, $password, 'copytube');
        //check connection to db
        if ($connection->connect_error) {
            die("connection failed: " . $connection->connect_error);
        }
        //Get password from db
        $sql = "SELECT `username`, `password` FROM `users` WHERE `username`='$usernameInput'";
        //$result = query of $sql
        $result = $connection->query($sql);
        //If query fails, die. If not then get results
        if ($result == false){
            die("PHP Response SQL: The query of sql is false.
        Result is: $result.
        Sql is: $sql.");
        } else {
            $response = $result->fetch_all(MYSQLI_ASSOC);
            $connection->close();
        }
        //Compare DB and User Password
        if (password_verify($passwordInput, $response[0]['password'])){
            $verify = "true";
            print_r($verify);
        } else {
            $verify = "false";
            print_r($verify);
        }
    }
}
