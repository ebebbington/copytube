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
$email = $_POST['email'];
$passwordInput = $_POST['password'];
//create connection to db
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
if ($connection->connect_error) {
    die("connection failed: " . $connection->connect_error);
}
//Get password from db
$sql = "SELECT email, password FROM users WHERE email='$email'";
//$result = query of $sql
$result = $connection->query($sql);
//If query fails, die. If not then get results
if ($result == false){
    print_r(json_encode(false));
} else {
    $response = $result->fetch_all(MYSQLI_ASSOC);
    // check if input is even in db
    if (count($response) === 0) {
        print_r(json_encode(false));
    } else {
        //Compare DB and User Password
        $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);
        if (password_verify($passwordInput, $response[0]['password'])) {
            $sql = "UPDATE users SET loggedIn = 0 WHERE username = '$usernameInput'"; // todo :: remove me when user status is better
            $connection->query($sql);
            $connection->close();
            setcookie(username, $_POST['username']);
            print_r(json_encode(true));
        } else {
            print_r(json_encode(false));
        }
    }
}