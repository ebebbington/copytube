<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:47
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/controllers/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/validate.php';

/*
 *  -----------------------------------------------------------------------------------------------------------------------------------
 * |                   Supporting notes                                                                                                |
 * |                                                                                                                                   |
 * | 1. Check database connection                                                                                                      |
 * |    $this->databaseConnectionStatus = $this->db->connection->ping(); // This can't go in construct because it will always equal 1  |
   |    $this->databaseConnectionStatus === NULL ? print_r('DB Status: ' . false) : print_r('DB Status: ' . true);                     |
 * |                                                                                                                                   |
 *  -----------------------------------------------------------------------------------------------------------------------------------
 */

class User
{
    //
    // SQL Queries
    //
    const GET_USER_ID = "SELECT user_id FROM sessions WHERE session_id_2 = ?";
    const LOGOUT_USER = "UPDATE users SET loggedIn = 1 WHERE id = ?";
    const DELETE_SESSION = "DELETE FROM sessions WHERE user_id = ?";
    const GET_CURRENT_USER = "SELECT * FROM users WHERE email_address = ?";
    const INSERT_NEW_SESSION = "INSERT INTO sessions (session_id_1, session_id_2, user_id) VALUES (?, ?, ?)";
    const UPDATE_LOGIN_ATTEMPTS = "UPDATE users SET login_attempts = ? WHERE email_address = ?";
    const GET_ALL_USERS = "SELECT * FROM users";
    const SET_LOGGED_IN = "UPDATE users SET loggedIn = 0 WHERE email_address = ?";

    //
    // Static Variables
    //
    private $db;
    private $validate;
    private $databaseConnectionStatus;
    public $username;
    public $email;

    //
    // Initialise Data
    //
    public function __construct() {
        $this->db = new Database();
        $this->db->openDatabaseConnection(); // DOES OPEN THE CONNECTION WITHOUT ANY OTHER LINES OF CODE AND CAN CLOSE FURTHER DOWN THE LINE
        $this->validate = new Validate();
    }

    //
    // Run Login Function
    //
    public function login ($postData) {
        $emailInput = $postData['email'];
        $passwordInput = $postData['password'];
        $query = $this->db->connection->prepare(self::GET_CURRENT_USER);
        $query->bind_param('s', $emailInput);
        $query->execute();
        $user = [];
        $query->bind_result($user[0]['id'], $user[0]['username'], $user[0]['email'], $user[0]['password'], $user[0]['loggedIn'], $user[0]['loginAttempts']);
        $query->fetch(); // This is needed, otherwise if i try to access the binded variables the output is ""
        if ($user[0]['id'] === NULL) {
            // Means ive used the wrong email
            print_r(json_encode(['login', false]));
        } else {
            // Means correct email is given
            if (password_verify($passwordInput, $user[0]['password'])) {
                if ($user[0]['loginAttempts'] === 0) {
                    $this->lockoutEmail($postData);
                    print_r(json_encode(['lockout', true]));
                } else {
                    session_start();
                    $sessionId1 = random_bytes(16);
                    $sessionId1 = bin2hex($sessionId1);
                    $sessionId2 = random_bytes(16);
                    $sessionId2 = bin2hex($sessionId2);
                    // Assign data when creating the cookies
                    setcookie('sessionId1', $sessionId1, time() + 3200, '/');
                    setcookie('sessionId2', $sessionId2, null, '/');
                    // Insert data into DB
                    $this->db->connection = new mysqli('localhost', 'root', 'password', 'copytube');
                    $query = $this->db->connection->prepare(self::INSERT_NEW_SESSION);
                    $id = $user[0]['id'];
                    $query->bind_param('ssi', $sessionId1, $sessionId2, $id);
                    $query->execute();
                    // Update loggedIn
                    $query = $this->db->connection->prepare(self::SET_LOGGED_IN);
                    $query->bind_param('s', $user[0]['email']);
                    $query->execute();
                    print_r(json_encode(['login', true]));
                }
            } else {
                // Password not the same
                $this->db->connection = new mysqli('localhost', 'root', 'password', 'copytube');
                $query = $this->db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
                $loginAttempts = $user[0]['loginAttempts'] - 1;
                $query->bind_param('is', $loginAttempts, $emailInput);
                $query->execute();
                print_r(json_encode(['login', false]));
            }
        }
        $this->db->closeDatabaseConnection();
    }

    //
    // Run Logout function
    //
    public function logout () {
        if (isset($_COOKIE['sessionId2'])) {
            $sessionId = $_COOKIE['sessionId2'];
            $this->db->openDatabaseConnection();
            $query = $this->db->connection->prepare(self::GET_USER_ID);
            $query->bind_param('s', $sessionId);
            $query->execute();
            $user = [];
            $query->bind_result($user[0]['user_id']);
            $query->fetch(); // This is needed, otherwise if i try to access the binded variables the output is ""
            $userId = $user[0]['user_id'];
            $this->db->connection = new mysqli('localhost', 'root', 'password', 'copytube');
            $query = $this->db->connection->prepare(self::LOGOUT_USER);
            $query->bind_param('i', $userId);
            $query->execute();
            $query = $this->db->connection->prepare(self::DELETE_SESSION);
            $query->bind_param('i', $userId);
            $query->execute();
            setcookie("sessionId1", "", time() - 3600, '/');
            setcookie('PHPSESSID', '', time()-3600, '/');
            setcookie("sessionId2", "", time() - 3600, '/');
            setcookie("name", "", time() - 3600, '/');
            session_abort();
            session_unset();
            print_r(json_encode(['logout', true]));
        } else {
            print_r(json_encode(['logout', true]));
        }
        $this->db->closeDatabaseConnection();
    }

    //
    // Run Register function
    //
    public function register ($postData) {
        $this->validate->validateUsername($postData);
        $this->db->closeDatabaseConnection();
    }

    //
    // Tell user Account is Locked
    //
    private function lockoutEmail ($postData) {
        $receiver = $postData['email'];
        $subject = 'Account Locked Out';
        $message = "Your account $receiver has been locked out on CopyTube. To recover it please visit http://localhost/copytube/public/view/recover.html";
        $header = 'From: noreply@copytube.com';
        mail($receiver, $subject, $message, $header);
    }

    //
    // Tell user Account is Recovered
    //
    private function recoverEmail ($postData) {
        $receiver = $postData['email'];
        $subject = 'Account Recovered';
        $message = "Your account $receiver has been recovered on CopyTube.";
        $header = 'From: noreply@copytube.com';
        mail($receiver, $subject, $message, $header);
    }

    //
    // Run Recover function
    //
    public function recover ($postData) {
        $email = $postData['email'];
        $password = $postData['password'];
        $query = $this->db->connection->prepare(self::GET_CURRENT_USER);
        $query->bind_param('s', $email);
        $query->execute();
        $user = [];
        $query->bind_result($user[0]['id'], $user[0]['username'], $user[0]['email'], $user[0]['password'], $user[0]['loggedIn'], $user[0]['loginAttempts']);
        $query->fetch();
        if ($user[0]['id'] === NULL){
            // User doesn't exist
            print_r(json_encode(['email', false]));
        } else {
            // Validate
            if ($user[0]['loginAttempts'] === 0) {
                if (password_verify($password, $user[0]['password'])) {
                    $query = $this->db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
                    $loginAttempts = 3;
                    $query->bind_param('is', $loginAttempts, $email); // fixme :: Query returns false above
                    $query->execute();
                    $this->recoverEmail($postData);
                    print_r(json_encode(['password', true]));
                } else {
                    print_r(json_encode(['password', false]));
                }
            } else {
                // User should not be recovering as it's already recovered
                print_r(json_encode(['email', 'No recovering is needed']));
            }
        }
        $this->db->closeDatabaseConnection();
    }

}