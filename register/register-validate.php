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
$maxLength = 40;
/* Validation - Start */
if ($usernameInput === '' || $usernameInput >= ($maxLength + 1) || trim($usernameInput) === 0 || $usernameInput === null) {
    print_r(false);
} else {
    if ($passwordInput === '' || $passwordInput >= ($maxLength + 1) || trim($passwordInput) === 0 || $passwordInput === null) {
        print_r(false);
    } else {
        if (isset($_POST['submit'])) {
            if (empty($_POST['username'])) {
                echo "<script>$('#danger').text('Please enter the correct credentials')</script>";
            } else if (!preg_match('/^[a-zA-Z ]*$/', $_POST['username'])) {
                echo "<script>$('#danger').text('Only letters and whitespaces allowed')</script>";
            }
        }
        // Hash
        $hash = password_hash($passwordInput, PASSWORD_BCRYPT);
        //create connection
        $connection = new mysqli($serverName, $username, $password, 'copytube');
        //check connection
        if ($connection->connect_error) {
            die("connection to database failed: " + $connection->connect_error);
        }
        //if connection works, set variable to string of inserting data
        $sql = "INSERT INTO users (username, password, loggedIn) VALUES ('$usernameInput', '$hash', 1)";
        //set this data in the database
        $connection->query($sql);
        $connection->close();
        echo "<script>window.location.replace('http://localhost/copytube/register/register.html');</script>";
    }
}