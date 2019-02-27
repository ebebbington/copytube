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
    const GET_FULL_USER = "SELECT * FROM users WHERE email_address = ?";
    const INSERT_NEW_SESSION = "INSERT INTO sessions (session_id, username_id, users_username_id) VALUES (?, ?, ?)";
    const UPDATE_LOGIN_ATTEMPTS = "UPDATE users SET login_attempts = 3 WHERE email_address = ?";
    const GET_ALL_USERS = "SELECT * FROM users";

    //
    // Run Login Function
    //
    public function login () {
        $email = $_POST['email'];
        $passwordInput = $_POST['password'];
        $db = new Database();
        $db->openDatabaseConnection();
        // MAIN METHOD
        /*
        $query = $db->connection->prepare(self::GET_FULL_USER);
        $query->bind_param('s', $email);
        $query->execute();
        */

        // METHOD 2
        /*
        $test1 = $db->connection->prepare(self::GET_FULL_USER);
        $test1->bind_param('s', $email);
        $test1->execute();
        var_dump($test1);
        $test2 = $test1->get_result();
        var_dump($test2->username); // try to access object values here
        $test3 = $test1->fetch_all(MYSQLI_ASSOC);
        var_dump($test3);
        */

        // METHOD 3 - SO TEST1 DOES HAVE A ROW AND I ASSUME CONTAINS THE DATA
        /*
        $test1 = $db->connection->prepare(self::GET_FULL_USER);
        $test1->bind_param('s', $email);
        $test1->execute();
        var_dump($test1);
        $test1->store_result(); // this returns false
        var_dump($test1->fetch()); // error: cant use an object as an array
        $numberOfRows = $test1->num_rows;
        var_dump('No of rows: ' . $numberOfRows);
        $fetch = $test1->fetch();
        var_dump('FETCHED: ' . $fetch);
        $test3 = $fetch[0]['id'];
        var_dump($test3);
        */

        // METHOD 4 - STANDARD SELECT
        $query = $db->connection->prepare(self::GET_FULL_USER);
        $query->bind_param('s', $email);
        $query->execute();

        $dbEmail = '';
        $dbPassword = '';
        $results4 = '';
        $results5 = '';
        $results6 = '';
        $results7 = '';
        var_dump($query->result);
        $query->bind_result($results2, $results3, $results4, $results5, $results6, $results7); // Try BIND, STORE, GET. FETCH, FETCHALL,
        $query->fetch(); // This is needed, otherwise if i try to access the binded variables the output is ""
        var_dump($results4);
        var_dump($query);
        var_dump($results3);
        $test2 = $query->fetch_all(MYSQLI_ASSOC);
        var_dump($test2);


        $users = $query->fetch_all(MYSQLI_ASSOC); // todo :: GOT HERE, fetch all doesn't work?
        if (password_verify($passwordInput, $users[0]['password'])) {
            if ($users[0]['loginAttempts'] === '0') {
                $db->closeDatabaseConnection();
                $this->lockoutEmail();
                print_r(json_encode(['lockout', true]));
            } else {
                session_start();
                $sessionId = random_bytes(16);
                $sessionId = bin2hex($sessionId);
                $userId = random_bytes(16);
                $userId = bin2hex($userId);
                $dbUserId = $user[0]['id'];
                // Remove unneeded session cookie
                // Assign data when creating the cookies
                setcookie('sessionId', $sessionId, time() + 3200, '/');
                setcookie('usernameId', $userId, null, '/');
                // Insert data into DB
                $query = $db->connection->prepare(self::GET_FULL_USER);
                $query->execute($sessionId, $userId, $dbUserId);
                $db->closeDatabaseConnection();
                print_r(json_encode(['login', true]));
            }
        } else {
            // Password not the same
            $db->closeDatabaseConnection();
            print_r(json_encode(['password', false]));
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
        $validate = new Validate();
        $validate->validateUsername();
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
        $query = $db->connection->prepare(self::GET_FULL_USER);
        $query->execute($email);
        $user = $query->fetch_all(MYSQLI_ASSOC);
        if ($query === false || count($user) === 0){
            // User doesn't exist
            print_r(json_encode(['user', false]));
        } else {
            // Validate
            if ($user[0]['login_attempts'] === '0') {
                if (password_verify($password, $user[0]['password'])) {
                    $query = $db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
                    $query->execute($email);
                    $this->recoverEmail();
                    print_r(json_encode(['recover', true]));
                } else {
                    print_r(json_encode(['recover', false]));
                }
            } else {
                // User should not be recovering
                exit();
            }
        }
    }

}