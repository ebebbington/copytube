<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 21/02/2019
 * Time: 15:07
 */

$serverName = "localhost";
$username = "root";
$password = "password";
$email = $_POST['email'];
$passwordInput = $_POST['password'];

function sendRecoverEmail ($email) {
    $receiver = $email;
    $subject = 'Account Recovered';
    $message = "Your account $receiver has been recovered on CopyTube.";
    $header = 'From: noreply@copytube.com';
    mail($receiver, $subject, $message, $header);
    print_r('true');
}

//create connection to db
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
if ($connection->connect_error) {
    die("connection failed: " . $connection->connect_error);
}
//Get password from db
$sql = "SELECT email_address, login_attempts FROM users WHERE email_address='$email'";
//$result = query of $sql
$result = $connection->query($sql);
//If query fails, die. If not then get results
if ($result == false){
    print_r('false');
} else {
    $response = $result->fetch_all(MYSQLI_ASSOC);
    // check if input is even in db
    if (count($response) === 0) {
        print_r('false');
    } else {
        if ($response[0]['login_attempts'] === '0') {
            $sql = "UPDATE users SET login_attempts = 3 WHERE email_address = '$email'";
            $connection->query($sql);
            sendRecoverEmail($email);
            print_r('true');
        } else {
            exit();
        }
    }
}