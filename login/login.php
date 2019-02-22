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

// SEND EMAIL
// Configuration is set up in php.ini and sendmail.ini. I allowed all access in google account and this is the code to do what i want.
function sendLockoutEmail ($email) {
    $receiver = $email;
    $subject = 'Account Locked Out';
    $message = "Your account $receiver has been locked out on CopyTube. To recover it please visit http://localhost/copytube/recover/recover.html";
    $header = 'From: no-reply@copytube.com';
    mail($receiver, $subject, $message, $header);
    print_r('lockout');
}
if ($response[0]['login_attempts'] === '0') {
    sendLockoutEmail($email);
}
if (password_verify($passwordInput, $response[0]['password'])) {
    session_start();
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
    $sql = "INSERT INTO sessions (session_id, username_id, users_username_id) VALUES ('$sessionId', '$userId', $dbUserId)";
    $connection->query($sql);
    $connection->close();
    print_r('true');
} else {
    // calls send email function
}