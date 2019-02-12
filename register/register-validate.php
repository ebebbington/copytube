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
$error = false;
$errorMsg = '';
// Validation
/* todo :: Figure out how to append text such as: $('#username-error').append(<p>Error</p>) THEN if i figure this out
replace scripts and embed the if statements within each other and if an input is wrong change $error to true.
Another option would be to place this code in register.php if it's safe. */
if (isset($_POST['submit'])) {
    // Username
    if ($name >= ($maxLength + 1 || trim($name) === 0 || $name === null || empty($name))) {
        echo "<script>alert('Enter a correct username');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Username
    if (!preg_match('/^[a-zA-Z ]*$/', $name)) {
        echo "<script>alert('Only whitespaces and letters are allowed');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Email
    if (trim($email) === 0 || $email === null || empty($email)) {
        echo "<script>alert('Enter a correct email');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Incorrect email format');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Password
    if (empty($pass) || $pass >= ($maxLength + 1) || trim($pass) === 0 || $pass === null) {
        echo "<script>alert('Enter a correct password');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Password
    if (strlen($pass) <= '8') {
        echo "<script>alert('Password must contain more than 8 characters');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Password
    if(!preg_match("#[0-9]+#",$password)) {
        echo "<script>alert('Password must contain at least one number');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Password
    if(!preg_match("#[A-Z]+#",$password)) {
        echo "<script>alert('Password must contain at least one capital letter');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    // Password
    if(!preg_match("#[a-z]+#",$password)) {
        echo "<script>alert('Password must contain at least one lowercase letter');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
    if ($error === false) {
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
    } else {
        echo "<script>alert('Incorrect credentials');</script>";
        echo "<script>window.location.replace('http://localhost/copytube/register/register.php');</script>";
    }
}

// Start of method 2
// Username
/* if ($name >= ($maxLength + 1 || trim($name) === 0 || $name === null || empty($name))) {
    $error = true;
    $usernameError = 'Enter a username';
} else {
    // Username
    if (!preg_match('/^[a-zA-Z ]*$/', $name)) {
        $error = true;
        $usernameError = 'Only letters and white spaces allowed';
    } else {
        // Email
        if (trim($email) === 0 || $email === null || empty($email)) {
            $error = true;
            $emailError = 'Enter an email';
        } else {
            // Email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = true;
                $emailError = 'Incorrect email format';
            } else {
                // Password
                if (empty($pass) || $pass >= ($maxLength + 1) || trim($pass) === 0 || $pass === null) {
                    $error = true;
                    $passwordError = 'Enter a password';
                } else {
                    // Password
                    if (strlen($pass) <= '8') {
                        $error = true;
                        $passwordError = 'Password must contain more than 8 characters';
                    } else {
                        // Password
                        if(!preg_match("#[0-9]+#",$password)) {
                            $error = true;
                            $passwordError = 'Password must contain at least one number';
                        } else {
                            // Password
                            if(!preg_match("#[A-Z]+#",$password)) {
                                $error = true;
                                $passwordError = 'Password must contain at least one capital letter';
                            } else {
                                // Password
                                if(!preg_match("#[a-z]+#",$password)) {
                                    $error = true;
                                    $passwordError = 'Password must contain at least one lowercase letter';
                                } else {
                                    if ($error === false) {
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
                }
            }
        }
    }
} */