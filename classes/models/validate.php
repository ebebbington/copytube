<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 26/02/2019
 * Time: 11:23
 */

include_once 'smtp-email-check.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/controllers/database.php';

class Validate
{
    //
    // SQL Queries
    //
    const ADD_NEW_USER = "INSERT INTO users (username, email_address, password, loggedIn, login_attempts) VALUES (?, ?, ?, ?, ?)";
    const SELECT_ALL_USERS = "SELECT * FROM users";

    //
    // Initialise data
    //
    private $maxlength = 40;

    //
    // Initialise
    //
    public function __construct() {
        $this->db = new Database();
        $this->db->openDatabaseConnection();
    }

    //
    // Validate Username
    //      v
    // Verify Email
    //      v
    // Validate Email
    //      v
    // Validate Password
    //      v
    // Register User
    public function validateUsername() {
        if (isset($_POST['username'])) {
            $username = $_POST['username'];
            if (strlen($username) > $this->maxlength || trim($username) === 0 || $username === null
              || empty($username)
            ) {
                print_r(json_encode(array('username', 'Enter a Username')));
            } else {
                if (!preg_match('/^[a-zA-Z ]*$/', $username)) {
                    print_r(json_encode(['username', 'Only letters and whitespaces allowed']));
                } else {
                    if (!filter_var($username, FILTER_SANITIZE_STRING)) {
                        print_r(json_encode(['username', 'Remove tags']));
                    } else {
                        // cHECK if username exists
                        $query = $this->db->connection->query(self::SELECT_ALL_USERS);
                        $users = $query->fetch_all(MYSQLI_ASSOC);
                        $usernameExists = false;
                        for ($i = 0, $l = sizeof($users); $i < $l; $i++) {
                            // IM A GENIUS
                            if ($username === $users[$i]['username']) {
                                $usernameExists = true;
                                print_r(json_encode(['username', 'Username already exists']));
                            }
                        }
                        if ($usernameExists === false) {
                            $username = mysqli_real_escape_string($this->db->connection, $username);
                            $this->db->closeDatabaseConnection();
                            $this->verifyEmail($username);
                        }
                    }
                }
            }
        } else {
            print_r(json_encode(['username', 'Please fill in the username field']));
        }
        $this->db->closeDatabaseConnection();
    }

    private function verifyEmail ($username) {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            // Set email verifying data
            try {
                $verifyEmail = new verifyEmail();
                $verifyEmail->setStreamTimeoutWait(20);
                /* Below are debugging tools, disable them for verify email function to run properly */
                // $verifyEmail->Debug = true; // Creates an alert currently with the process
                // $verifyEmail->Debugoutput = 'html'; // Displays js code error in console
                $verifyEmail->setEmailFrom($email);
            } catch (exception $e) {
                print_r(json_encode(['email', 'Could not validate email address']));
                exit();
            }
            // todo :: this class seems a bit dodgey, it doesn't work anymore
            if ($verifyEmail->check($email)) {
                $this->validateEmail($username);
            } else {
                if ($verifyEmail::validate($email)) {
                    print_r(json_encode(['email', 'Email is valid but does not exist']));
                } else {
                    print_r(json_encode(['email', 'Email is not valid and does not exist']));
                }
            }
        } else {
            print_r(json_encode(['email', 'Please fill in the email field']));
        }
    }

    private function validateEmail ($username)
    {
        $email = $_POST['email'];
        if (trim($email) === 0 || $email === null || empty($email)) {
            print_r(json_encode(['email', 'Enter an email']));
        } else {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                print_r(json_encode(['email', 'Incorrect email format']));
            } else {
                if (!filter_var($email, FILTER_SANITIZE_EMAIL)) {
                    print_r(json_encode(['email', 'Remove tags']));
                } else {
                    $this->db->openDatabaseConnection();
                    $query = $this->db->connection->query(self::SELECT_ALL_USERS);
                    $users = $query->fetch_all(MYSQLI_ASSOC);
                    $emailExists = false;
                    for ($i = 0, $l = sizeof($users); $i < $l; $i++) {
                        // IM A GENIUS
                        if ($email === $users[$i]['email_address']) {
                            $emailExists = true;
                            print_r(json_encode(['email', 'Email already exists']));
                            break;
                        }
                    }
                    if ($emailExists === false) {
                        $email = mysqli_real_escape_string($this->db->connection, $email);
                        $this->validatePassword($username, $email);
                    }
                }
            }
        }
        $this->db->closeDatabaseConnection();
    }

    private function validatePassword ($username, $email) {
        if (isset($_POST['password'])) {
            $password = $_POST['password'];
            // Password
            if (trim($password) === 0 || $password=== null || empty($password)) {
                print_r(json_encode(['password', 'Enter a password']));
            } else {
                if (strlen($password) < 8) {
                    print_r(json_encode(['password', 'Password must contain 8 or more characters']));
                } else {
                    // Password - Find a number - personal algorithm
                    $numberFound = false;
                    while ($numberFound !== true) {
                        for ($i = 0, $l = strlen($password); $i < $l; $i++) {
                            $value = $password[$i];
                            if (is_numeric($value)) {
                                $numberFound = true;
                                break;
                            }
                        }
                        if ($numberFound !== true) {
                            print_r(json_encode(['password', 'Must contain at least one number']));
                            break;
                        }
                    }
                    if ($numberFound === true) {
                        // Password - Self created algorithm - MADE IT WORK - ctype_[] didn't account for other characters than letters
                        // This grabs all upper and lower case letters and then makes sure the end result contains an upper and lower
                        $letterRange1 = range('a', 'z');
                        $letterRange2 = range('A', 'Z');
                        $passOnlyLetters = [];
                        for ($i = 0, $l = strlen($password); $i < $l; $i++) {
                            if (in_array($password[$i], $letterRange1)) {
                                array_push($passOnlyLetters, $password[$i]);
                            }
                            if (in_array($password[$i], $letterRange2)) {
                                array_push($passOnlyLetters, $password[$i]);
                            }
                        }
                        if (ctype_upper(implode($passOnlyLetters)) || ctype_lower(implode($passOnlyLetters))) {
                            print_r(json_encode(['password', 'Must contain at least one upper and lowercase character']));
                        } else {
                            if ($username === $password) {
                                print_r(json_encode('password', 'Password cannot be the same as the username'));
                            } else {
                                // Password
                                if (strpos($password, $username) !== false) {
                                    print_r(json_encode(['password', 'Password cannot contain username']));
                                } else {
                                    if (!filter_var($password, FILTER_SANITIZE_STRING)) {
                                        print_r(json_encode('password', 'Remove tags'));
                                    } else {
                                        $this->db->openDatabaseConnection();
                                        $password = mysqli_real_escape_string($this->db->connection, $password);
                                        $this->registerUser($username, $email, $password);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            print_r(json_encode(['password', 'Please set a password']));
        }
        $this->db->closeDatabaseConnection();
    }

    private function registerUser ($username, $email, $password) {
        $this->db->openDatabaseConnection();
        $loggedIn = 1;
        $loginAttempts = 3;
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query = $this->db->connection->prepare(self::ADD_NEW_USER);
        $query->bind_param('sssss', $username, $email, $hash, $loggedIn, $loginAttempts);
        $query->execute();
        if ($query->affected_rows < 1 || $query->affected_rows > 1) {
            return json_encode(['Query did not affect the database or created more than one field']);
        } else {
            print_r(json_encode(['user', 'Successfully registered an account']));
        }
        $this->db->closeDatabaseConnection();
    }

}