<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 26/02/2019
 * Time: 11:23
 */

class Validate
{
    //
    // SQL Queries
    //
    const ADD_NEW_USER = "INSERT INTO users (username, email_address, password, loggedIn, login_attempts) VALUES (?, ?, ?, ?)";

    //
    // Initialise data
    //
    private $maxlength;

    private function __construct() {
        $this->maxLength = 40;
    }

    public function validateUsername() {
        $username = $_POST['username'];
        $test = $this->maxLength;

        $this->verifyEmail();
    }

    private function verifyEmail () {
        include_once 'smtp-email-check.php';

        $this->validateEmail();
    }

    private function validateEmail () {
        $email = $_POST['email'];

        $this->validatePassword();
    }

    private function validatePassword () {


        $password = $_POST['password'];
    }

}