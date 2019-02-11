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
$name = $_POST['username'];
$email = $_POST['email'];
$pass = $_POST['password'];
$maxLength = 40;
$errorMsg = 'Please enter the correct credentials. Only letters and whitespaces are allowed for your username';
// Validation
if (isset($_POST['submit'])) {
    // Username
    if ($name >= ($maxLength + 1 || trim($name) === 0 || $name === null || empty($name))) {
        echo "<script>alert('Incorrect credentials. Only whitespaces and letters in username');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    } else {
        // Username
        if (!preg_match('/^[a-zA-Z ]*$/', $name)) {
            echo "<script>alert('Incorrect credentials. Only whitespaces and letters in username');</script>";
            echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
        } else {
            // Email
            if (trim($email) === 0 || $email === null || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "<script>alert('Incorrect credentials. Only whitespaces and letters in username');</script>";
                echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
            } else {
                // Password
                if (empty($pass) || $pass >= ($maxLength + 1) || trim($pass) === 0 || $pass === null) {
                    echo "<script>alert('Incorrect credentials. Only whitespaces and letters in username');</script>";
                    echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
                } else {
                    // All validation is correct
                    $hash = password_hash($pass, PASSWORD_BCRYPT);
                    //create connection
                    $connection = new mysqli($serverName, $username, $password, 'copytube');
                    //check connection
                    if ($connection->connect_error) {
                        die("connection to database failed: " + $connection->connect_error);
                    }
                    //if connection works, set variable to string of inserting data
                    $sql = "INSERT INTO users (username, password, loggedIn) VALUES ('$name', '$hash', 1)";
                    //set this data in the database
                    $connection->query($sql);
                    $connection->close();
                    echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
                }
            }
        }
    }
}