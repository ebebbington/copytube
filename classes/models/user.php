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
    const ADD_USER = ""

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
        // todo :: Run logout code
        if (isset($_COOKIE['usernameId'])) {
            $db = new Database();
            $db->openDatabaseConnection();
            $id = $_COOKIE['usernameId'];

        } else {
            echo "<script>window.location.replace('http://localhost/copytube/login/login.html')";
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
    public function recover () {
        // todo :: Run recover code
    }

}