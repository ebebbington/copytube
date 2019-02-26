<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 06/02/2019
 * Time: 12:12
 */

include_once '../classes/class.verifyEmail.php';

$serverName = "localhost";
$username = "root";
$password = "password";
$name = $_POST['name'];
$email = $_POST['email'];
$pass = $_POST['pass'];
$maxLength = 40;
$error = false;
$errorMsg = null;
$numberFound = false;
// Set email verifying data
try {
    $verifyEmail = new verifyEmail();
    $verifyEmail->setStreamTimeoutWait(20);
    /* Below are debugging tools, disable them for verify email function to run properly */
    // $verifyEmail->Debug = true; // Creates an alert currently with the process
    // $verifyEmail->Debugoutput = 'html'; // Displays js code error in console
    $verifyEmail->setEmailFrom($email);
} catch (exception $e) {
    $error = true;
    $errorMsg = array('email', 'Could not validate email address');
    print_r(json_encode($errorMsg));
}
if (isset($_POST['name'],$_POST['email'],$_POST['pass'])) {
    // Validation

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
                            // Password - Find a number - personal algorithm
                            while ($numberFound !== true) {
                                for ($i = 0, $l = strlen($pass); $i < $l; $i++) {
                                    $value = $pass[$i];
                                    if (is_numeric($value)) {
                                        $numberFound = true;
                                        break;
                                    }
                                }
                                if ($numberFound !== true) {
                                    $error = true;
                                    $errorMsg = array('pass', 'Must contain at least one number');
                                    print_r(json_encode($errorMsg));
                                    break;
                                }
                            }
                            if ($numberFound === true) {
                                // Password - Self created algorithm - MADE IT WORK - ctype_[] didn't account for other characters than letters
                                // This grabs all upper and lower case letters and then makes sure the end result contains an upper and lower
                                $letterRange1 = range('a', 'z');
                                $letterRange2 = range('A', 'Z');
                                $passOnlyLetters = [];
                                for ($i=0, $l=strlen($pass); $i<$l; $i++) {
                                    if (in_array($pass[$i], $letterRange1)) {
                                        array_push($passOnlyLetters, $pass[$i]);
                                    }
                                    if (in_array($pass[$i], $letterRange2)) {
                                        array_push($passOnlyLetters, $pass[$i]);
                                    }
                                }
                                if (ctype_upper(implode($passOnlyLetters)) || ctype_lower(implode($passOnlyLetters))) {
                                    $error = true;
                                    $errorMsg = array('pass', 'Must contain at least one upper and lowercase character');
                                    print_r(json_encode($errorMsg));
                                } else {
                                    // Password
                                    if ($name === $pass) {
                                        $error = true;
                                        $errorMsg = array('pass', 'Password cannot be the same as the username');
                                        print_r(json_encode($errorMsg));
                                    } else {
                                        // Password
                                        if (strpos($pass, $name)) {
                                            $error = true;
                                            $errorMsg = array('pass', 'Password cannot contain username');
                                            print_r(json_encode($errorMsg));
                                        } else {
                                                // Sanitization - Email
                                                if (!filter_var($email, FILTER_SANITIZE_EMAIL)) {
                                                    $error = true;
                                                    $errorMsg = array('email', 'Remove tags');
                                                    print_r(json_encode($errorMsg));
                                                } else {
                                                    // Sanitization - Password
                                                    if (!filter_var($pass, FILTER_SANITIZE_STRING)) {
                                                        $error = true;
                                                        $errorMsg = array('pass', 'Remove tags');
                                                        print_r(json_encode($errorMsg));
                                                    } else {
                                                        // Username - check if username and email already exist - Self created algo
                                                        $connection = new mysqli($serverName, $username, $password,
                                                          'copytube');
                                                        if ($connection->connect_error) {
                                                            die("connection failed: " + $connection->connect_error);
                                                        }
                                                        $sql
                                                          = "SELECT username, email_address FROM users";
                                                        $result = $connection->query($sql);
                                                        if ($result == false){
                                                            die ('broke');
                                                        }
                                                        $response = $result->fetch_all(MYSQLI_ASSOC);
                                                        for ($i = 0, $l = sizeof($response); $i < $l; $i++) {
                                                            // IM A GENIUS
                                                            if ($name === $response[$i]['username']) {
                                                                $i = $l;
                                                                $error = true;
                                                                $errorMsg = array(
                                                                  'name',
                                                                  'Username already exists'
                                                                );
                                                                print_r(json_encode($errorMsg));
                                                                break;
                                                            } else {
                                                                if ($email === $response[$i]['email_address']) {
                                                                    $i = $l;
                                                                    $error = true;
                                                                    $errorMsg = array('email', 'Email already exists');
                                                                    print_r(json_encode($errorMsg));
                                                                    break;
                                                                }
                                                            }
                                                            if ($i === $l) {
                                                                $complete = true;
                                                            }

                                                        }
                                                        if ($verifyEmail->check($email)) {
                                                            //set this data in the database
                                                            if ($error === false) {
                                                                // All validation is correct
                                                                $hash = password_hash($pass, PASSWORD_BCRYPT);
                                                                //create connection
                                                                $connection = new mysqli($serverName, $username,
                                                                  $password,
                                                                  'copytube');
                                                                //check connection
                                                                if ($connection->connect_error) {
                                                                    die("connection failed: "
                                                                      + $connection->connect_error);
                                                                }
                                                                // Escape the input
                                                                $name = mysqli_real_escape_string($connection, $name);
                                                                $email = mysqli_real_escape_string($connection, $email);
                                                                $pass = mysqli_real_escape_string($connection, $pass);
                                                                //if connection works, set variable to string of inserting data
                                                                $sql
                                                                  = "INSERT INTO users (username, email_address, password, loggedIn) VALUES ('$name', '$email', '$hash', 1)";
                                                                //set this data in the database
                                                                $connection->query($sql);
                                                                $connection->close();
                                                                print_r(json_encode($error));
                                                            }
                                                        } else {
                                                            if ($verifyEmail::validate($email)) {
                                                                $error = true;
                                                                $errorMsg = array('email', 'Email valid but does not exist');
                                                                print_r(json_encode($errorMsg));
                                                            } else {
                                                                $error = true;
                                                                $errorMsg = array('email', 'Email not valid and does not exist');
                                                                print_r(json_encode($errorMsg));
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
            }
        }
    }
}