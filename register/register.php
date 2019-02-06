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
// todo :: Validate server side
/* if ($usernameInput === '' || $usernameInput >= ($maxLength + 1) || $usernameInput.str.length === 0 || $usernameInput === null || $usernameInput === undefined) {
    alert('Enter correct credentials');
    return false;
  } else {
      if (password === '' || password > maxLength || password.trim().length === 0 || password === null || password === undefined) {
          alert('Enter correct credentials');
      return false;
    } else {
          return true;
    }
  }
} */
// Hash
$hash = password_hash($passwordInput, PASSWORD_BCRYPT);
//create connection
$connection = new mysqli($servername, $username, $password, 'copytube');
//check connection
if ($connection->connect_error) {
    die("connection to database failed: " + $connection->connect_error);
}
//if connection works, set variable to string of inserting data
$sql = "INSERT INTO users (username, password, loggedIn) VALUES ('$usernameInput', '$hash', 1)";

//set this data in the database
$connection->query($sql);
$connection->close();