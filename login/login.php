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
$maxLength = 40;
$usernameInput = $_POST['username'];
$passwordInput = $_POST['password'];

// Validation
if ($usernameInput === '' || $usernameInput >= ($maxLength + 1) || trim($usernameInput) === 0 || $usernameInput === null) {
    print_r(false);
} else {
    if ($passwordInput === '' || $passwordInput >= ($maxLength + 1) || trim($passwordInput) === 0 || $passwordInput === null) {
        print_r(false);
    } else {
        //create connection to db
        $connection = new mysqli($serverName, $username, $password, 'copytube');
        //check connection to db
        if ($connection->connect_error) {
            die("connection failed: " . $connection->connect_error);
        }
        //Get password from db
        $sql = "SELECT username, password FROM users WHERE username='$usernameInput'";
        //$result = query of $sql
        $result = $connection->query($sql);
        //If query fails, die. If not then get results
        if ($result == false){
        } else {
            $response = $result->fetch_all(MYSQLI_ASSOC);
            // check if input is even in db
            if (count($response) === 0) {
                echo "<script>alert('Incorrect credentials');</script>";
                echo "<script>window.location.replace('http://localhost/copytube/login/login.html');</script>";
            } else {
                //Compare DB and User Password
                $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);
                if (password_verify($passwordInput, $response[0]['password'])) {
                    echo "<script>alert('password verification succeeded');</script>";
                    $sql = "UPDATE users SET loggedIn = 0 WHERE username = '$usernameInput'";
                    $connection->query($sql);
                    $connection->close();
                    $open = "<script>window.location.replace('http://localhost/copytube/index/copytube.php')</script>";
                    echo $open;
                    setcookie(username, $_POST['username']);
                } else {
                    echo "<script>alert('password not verified = wrong');</script>";
                }
            }
        }
    }
}
