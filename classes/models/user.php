<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:47
 */

include_once '../controllers/database.php';

class User
{
    //
    // SQL Queries
    //
    const GET_USER = "SELECT users_username_id FROM sessions WHERE username_id = ?";
    const GET_CURRENT_USER = "SELECT users_username_id FROM sessions WHERE username_id = ?";
    const UPDATE_CURRENT_USER = "UPDATE users SET loggedIn = 1 WHERE id = ?";
    const DELETE_SESSION = "DELETE FROM sessions WHERE users_username_id = ?";

    //
    // Run Login Function
    //
    public function login () {
        // todo :: run validateEmail()
        function validateEmail () {
            // todo :: add code from login relating to email and if success then run validatePassword()
        }

        function validatePassword () {
            // todo :: add code from login relating to password and if success then run sendUser();
        }

        function sendUser() {
            // todo :: send user to index.php and use cookies and all that
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
            $query = $db->connection->prepare(self::GET_CURRENT_USER);
            $query->execute($id);
            $user = $query->fetch_all(MYSQLI_ASSOC);
            $id = $user[0]['users_username_id'];
            $query = $db->connection->prepare(self::UPDATE_CURRENT_USER);
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
        // todo :: Run register code with functions for verifying each field?
        function validateUsername() {

        }

        function validateEmail () {

        }

        function validatePassword () {

        }
    }

    //
    // Run Recover function
    //
    public function recoverAccount () {
        // todo :: add code to recover account
    }

}