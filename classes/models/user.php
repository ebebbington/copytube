<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:47
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/controllers/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/validate.php';

class User
{
    //
    // SQL Queries
    //
    const GET_USER_ID = "SELECT users_username_id FROM sessions WHERE username_id = ?";
    const LOGOUT_USER = "UPDATE users SET loggedIn = 1 WHERE id = ?";
    const DELETE_SESSION = "DELETE FROM sessions WHERE users_username_id = ?";
    const GET_CURRENT_USER = "SELECT * FROM users WHERE email_address = ?";
    const INSERT_NEW_SESSION = "INSERT INTO sessions (session_id, username_id, users_username_id) VALUES (?, ?, ?)";
    const UPDATE_LOGIN_ATTEMPTS = "UPDATE users SET login_attempts = ? WHERE email_address = ?";
    const GET_ALL_USERS = "SELECT * FROM users";

    //
    // Static Variables
    //
    private $db;
    private $validate;
    private $databaseConnectionStatus;

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
    public function login () {
        // Check connection status on startup
        $this->db->closeDatabaseConnection();
        $this->databaseConnectionStatus = $this->db->connection->ping(); // This can't go in construct because it will always equal 1
        $this->databaseConnectionStatus === NULL ? print_r('DB Status: ' . false) : print_r('DB Status: ' . true);
        return;

        $emailInput = $_POST['email'];
        $passwordInput = $_POST['password'];
        $db = new Database();
        $db->openDatabaseConnection();
        $query = $db->connection->prepare(self::GET_CURRENT_USER);
        $query->bind_param('s', $emailInput);
        $query->execute();
        // todo :: Must be a better simplified way to achieve the below section?
        $user = [];
        $query->bind_result($user[0]['id'], $user[0]['username'], $user[0]['email'], $user[0]['password'], $user[0]['loggedIn'], $user[0]['loginAttempts']);
        $query->fetch(); // This is needed, otherwise if i try to access the binded variables the output is ""
        if ($user[0]['id'] === NULL) {
            // Means ive used the wrong email
            $db->closeDatabaseConnection();
            print_r(json_encode(['login', false]));
        } else {
            // Means correct email is given
            if (password_verify($passwordInput, $user[0]['password'])) {
                if ($user[0]['loginAttempts'] === 0) {
                    $db->closeDatabaseConnection();
                    $this->lockoutEmail();
                    print_r(json_encode(['lockout', true]));
                } else {
                    session_start();
                    $sessionId = random_bytes(16);
                    $sessionId = bin2hex($sessionId);
                    // TODO :: the below userId could be removed as it seems useless
                    $userId = random_bytes(16);
                    $userId = bin2hex($userId);
                    // Assign data when creating the cookies
                    setcookie('sessionId', $sessionId, time() + 3200, '/');
                    setcookie('usernameId', $userId, null, '/');
                    // Insert data into DB
                    $db->openDatabaseConnection();
                    $query = $db->connection->prepare(self::INSERT_NEW_SESSION);
                    $query->bind_param('iii', $sessionId, $userId, $user[0]['id']);
                    $query->execute();
                    $db->closeDatabaseConnection();
                    print_r(json_encode(['login', true]));
                }
            } else {
                // Password not the same
                $db->openDatabaseConnection();
                $query = $db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
                $loginAttempts = $user[0]['loginAttempts'] - 1;
                $query->bind_param('is', $loginAttempts, $emailInput); // fixme :: call to member boolean - i fixed this by ading in a new db connection
                $query->execute();
                $db->closeDatabaseConnection();
                print_r(json_encode(['login', false]));
            }
        }
    }

    //
    // Run Logout function
    //
    public function logout () {
        if (isset($_COOKIE['usernameId'])) {
            $db = new Database();
            $db->openDatabaseConnection();
            $id = $_COOKIE['usernameId'];
            $query = $db->connection->prepare(self::GET_USER_ID);
            $query->execute($id);
            $user = $query->fetch_all(MYSQLI_ASSOC);
            $id = $user[0]['users_username_id'];
            $query = $db->connection->prepare(self::LOGOUT_USER);
            $query->execute($id);
            $query = $db->connection->prepare(self::DELETE_SESSION);
            $query->execute($id);
            $db->closeDatabaseConnection();

            setcookie("sessionId", "", time() - 3600, '/');
            setcookie('PHPSESSID', '', time()-3600, '/');
            setcookie("usernameId", "", time() - 3600, '/');
            setcookie("name", "", time() - 3600, '/');
            session_abort();
            session_unset();
            return json_encode([true]);
        } else {
            return json_encode([true]);
        }
    }

    //
    // Run Register function
    //
    public function register () {
        $this->validate->validateUsername();
    }

    //
    // Tell user Account is Locked
    //
    private function lockoutEmail () {
        $receiver = $_POST['email'];
        $subject = 'Account Locked Out';
        $message = "Your account $receiver has been locked out on CopyTube. To recover it please visit http://localhost/copytube/public/view/recover.html";
        $header = 'From: noreply@copytube.com';
        mail($receiver, $subject, $message, $header);
    }

    //
    // Tell user Account is Recovered
    //
    private function recoverEmail () {
        $receiver = $_POST['email'];
        $subject = 'Account Recovered';
        $message = "Your account $receiver has been recovered on CopyTube.";
        $header = 'From: noreply@copytube.com';
        mail($receiver, $subject, $message, $header);
    }

    //
    // Run Recover function
    //
    public function recover () {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $db = new Database();
        $db->openDatabaseConnection();
        $query = $db->connection->prepare(self::GET_CURRENT_USER);
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
                    $db->openDatabaseConnection();
                    $query = $db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
                    $loginAttempts = 3;
                    $query->bind_param('is', $loginAttempts, $email);
                    $query->execute();
                    $this->recoverEmail();
                    $db->closeDatabaseConnection();
                    print_r(json_encode(['password', true]));
                } else {
                    print_r(json_encode(['password', false]));
                }
            } else {
                // User should not be recovering as it's already recovered
                print_r(json_encode(['email', 'No recovering is needed']));
            }
        }
    }

}