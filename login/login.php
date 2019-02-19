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
$sql = "SELECT username, email_address, password, id FROM users WHERE email_address='$email'";
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
        echo "<script>alert('for some reason reach the success stage of php file')</script>";
        $passwordHash = password_hash($passwordInput, PASSWORD_BCRYPT);
        if (password_verify($passwordInput, $response[0]['password'])) {
            $connection->close();
            // Create a cookie of the users username - IT WORKS - the path is '/' to make it available everywhere - cookie expires in 1 hour
            // todo :: is the below the best practice to set a cookie?
            session_start();
            setcookie('username', $response[0]['username'], time()+3200, '/');
            setcookie('id', $response[0]['id'], null, '/');
            print_r(json_encode(true));
        } else {
            print_r(json_encode(false));
        }
    }
}