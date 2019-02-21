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

function sendLockoutEmail ($email) {
    $receiver = $email;
    $subject = 'Account Locked Out';
    $message = "Your account $receiver has been locked out on CopyTube. To recover it please visit http://localhost/copytube/recover/recover.html";
    $header = 'From: no-reply@copytube.com';
    mail($receiver, $subject, $message, $header);
    print_r('lockout');
}

//create connection to db
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection to db
if ($connection->connect_error) {
    die("connection failed: " . $connection->connect_error);
}
//Get password from db
$sql = "SELECT username, email_address, password, id, login_attempts FROM users WHERE email_address='$email'";
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
            sendLockoutEmail($email);
        } else {
            //Compare DB and User Password
            $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);
            if (password_verify($passwordInput, $response[0]['password'])) {
                // Create a cookie - IT WORKS - the path is '/' to make it available everywhere - cookie expires in 1 hour
                session_start();
                // Create data for cookies
                $sessionId = random_bytes(16);
                $sessionId = bin2hex($sessionId);
                $userId = random_bytes(16);
                $userId = bin2hex($userId);
                $dbUserId = $response[0]['id'];
                // Remove unneeded session cookie
                // Assign data when creating the cookies
                setcookie('sessionId', $sessionId, time() + 3200, '/');
                setcookie('usernameId', $userId, null, '/');
                // Insert data into DB
                $sql
                  = "INSERT INTO sessions (session_id, username_id, users_username_id) VALUES ('$sessionId', '$userId', $dbUserId)";
                $connection->query($sql);
                $connection->close();
                print_r('true');
            } else {
                $loginAttempt = ($response[0]['login_attempts']);
                if ($loginAttempt === '1') {
                    $sql = "UPDATE users SET login_attempts = 0 WHERE email_address = '$email'";
                    $connection->query($sql);
                    sendLockoutEmail($email);
                } else {
                    $loginAttempt = $loginAttempt - 1;
                    $sql = "UPDATE users SET login_attempts = '$loginAttempt' WHERE email_address = '$email'";
                    $connection->query($sql);
                    $connection->close();
                    print_r('false');
                }
            }
        }
    }
}