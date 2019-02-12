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
$name = $_POST['name'];
$email = $_POST['email'];
$pass = $_POST['pass'];
$maxLength = 40;
$error = false;
$errorMsg = null;
if (isset($_POST['name'],$_POST['email'],$_POST['pass'])) {
    // Validation
    // Username
    if (strlen($name) > $maxLength || trim($name) === 0 || $name === null || empty($name)) {
        $error = true;
        $errorMsg = array('name', 'Enter a username');
        print_r(json_encode($errorMsg));
    } else {
        // Username
        if (!preg_match('/^[a-zA-Z ]*$/', $name)) {
            $error = true;
            $errorMsg = array('name', 'Only letters and whitespaces allowed');
            print_r(json_encode($errorMsg));
        } else {
            // Email
            if (trim($email) === 0 || $email === null || empty($email)) {
                $error = true;
                $errorMsg = array('email', 'Enter an email');
                print_r(json_encode($errorMsg));
            } else {
                // Email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = true;
                    $errorMsg = array('email', 'Incorrect email format');
                    print_r(json_encode($errorMsg));
                } else {
                    // Password
                    if (trim($pass) === 0 || $pass === null || empty($pass)) {
                        $error = true;
                        $errorMsg = array('pass', 'Enter a password');
                        print_r(json_encode($errorMsg));
                    } else {
                        // Password
                        if (strlen($pass) < 8) {
                            $error = true;
                            $errorMsg = array('pass', 'Password must contain 8 or more characters');
                            print_r(json_encode($errorMsg));
                        } else {
                            // Password
                            if (!preg_match("/[0-9]$", $password)) {
                                $error = true;
                                $errorMsg = array('pass', 'Must contain at least one number');
                                print_r(json_encode($errorMsg));
                            } else {
                                // Password
                                if (!preg_match("#[a-zA-Z]+#", $password)) {
                                    $error = true;
                                    $errorMsg = array('pass', 'Must contain at least one upper and lowercase character');
                                    print_r(json_encode($errorMsg));
                                } else {
                                    // Sanitization - Name
                                    if (!filter_var($name, FILTER_SANITIZE_STRING)) {
                                        $error = true;
                                        $errorMsg = array('name', 'Remove tags');
                                        print_r(json_encode($errorMsg));
                                    } else {
                                        // Sanitization - Email
                                        if (!filter_var($email, FILTER_SANITIZE_EMAIL)) {
                                            $error = true;
                                            $errorMsg = array('email', 'Remove tags');
                                            print_r(json_encode($errorMsg));
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
                                                $sql
                                                  = "INSERT INTO users (username, password, loggedIn) VALUES ('$name', '$hash', 1)";
                                                //set this data in the database
                                                $connection->query($sql);
                                                $connection->close();
                                                print_r($errorMsg);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}