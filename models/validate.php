<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 26/02/2019
 * Time: 11:23
 */

// todo Add nice beautiful comments

include_once 'smtp-email-check.php';
include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/controllers/database.php';
include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/comments.php';

class Validate
{

    //
    // SQL Queries
    //
    const ADD_NEW_USER = "INSERT INTO users (username, email_address, password, loggedIn, login_attempts) VALUES (?, ?, ?, ?, ?)";

    const SELECT_ALL_USERS = "SELECT * FROM users";

    const GET_ALL_USERNAMES = "SELECT username FROM users";

    const GET_ALL_EMAILS = "SELECT email FROM users";

    //
    // Initialise data
    //
    private $maxlength = 40;

    private $db;

    private $usernames;

    //
    // Initialise
    //
    public function __construct()
    {
        $this->db = new Database();
    }

    public function isSet($data = [])
    {
        for ($i = 0; $i < (sizeof($data) - 1); $i++) {
            if ( ! $data[ $i ] || ! isset($data[ $i ])) {
                $result[ 'message' ] = 'Some data has not been set when passed into the request controller';

                return [false, "A value in the data array hasnt been set", null];
                break;
            }
        }

        return [true, 'All values are set', null];
    }

    public function validateComment($comment)
    {
        // Validate
        if (strlen($comment) > 400 || trim($comment) === 0 || $comment === null
          || empty($comment)
          || ! isset($comment)
        ) {
            return [false, 'Could not validate comment', null];
        }
        // Sanitise
        if ( ! filter_var($comment, FILTER_SANITIZE_STRING)) {
            return [false, 'Could not sanitise comment', null];
        }
        try {
            $this->db->openDatabaseConnection();
            $comment = mysqli_real_escape_string($this->db->connection, $comment);
            $this->db->closeDatabaseConnection();
            return [true, 'Successfully validated comment', $comment];
        } catch (Exception $e) {
            new Mail('edward.bebbington@intercity.technology', 'Error: DB', $e);
            return [false, 'Error produced when using the DB to sanitise comment', $e];
        }
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
    public function validateUsername($username)
    {
        // Validate
        if (strlen($username) > $this->maxlength || trim($username) === 0
          || $username === null
          || empty($username)
          || ! isset($username)
        ) {
            return [false, 'Could not validate username', 'username'];
        }
        // Check RegEx
        if ( ! preg_match('/^[a-zA-Z ]*$/', $username)) {
            return [false, 'Only letters and whitespaces allowed', 'username'];
        }
        // Sanitise
        if ( ! filter_var($username, FILTER_SANITIZE_STRING)) {
            return [false, 'Remove tags', 'username'];
        }
        // Check if username exists
        try {
            $this->db->openDatabaseConnection();
            $query     = $this->db->connection->query(self::GET_ALL_USERNAMES);
            $this->usernames = $query->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            return [false, 'Database connection failed when getting usernames', $e];
        } finally {
            $this->db->closeDatabaseConnection();
        }
        for ($i = 0, $l = sizeof($this->usernames); $i < $l; $i++) {
            // IM A GENIUS
            if ($username === $this->usernames[ $i ]) {
                return [false, 'Username already exists', 'username'];
                break;
            }
        }
        $username = mysqli_real_escape_string($this->db->connection, $username);

        return [true, 'Successfully validated username', $username];
    }

    private function verifyEmail(
      $email
    ) {
        if (isset($email)) {
            // Set email verifying data
            try {
                $verifyEmail = new verifyEmail();
                $verifyEmail->setStreamTimeoutWait(20);
                /* Below are debugging tools, disable them for verify email function to run properly */
                // $verifyEmail->Debug = true; // Creates an alert currently with the process
                // $verifyEmail->Debugoutput = 'html'; // Displays js code error in console
                $verifyEmail->setEmailFrom($email);
            } catch (exception $e) {
                return ['email', 'Could not validate email address'];
            }
            if ($verifyEmail->check($email)) {
                return true;
            } else {
                if ($verifyEmail::validate($email)) {
                    return ['email', 'Email is valid but does not exist IRL'];
                } else {
                    return ['email', 'Email is not valid and does not exist IRL'];
                }
            }
        } else {
            return ['email', 'Please fill in the email field'];
        }
    }

    private function validateEmail($email)
    {
        // Validate
        if (trim($email) === 0 || $email === null || empty($email)
          || ! filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            return ['email', 'Enter an email following the correct format'];
        }
        // Sanitise
        if ( ! filter_var($email, FILTER_SANITIZE_EMAIL)) {
            return ['email', 'Remove tags'];
        }
        // RegEx
        if ( ! preg_match("[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$", $email)) {
            return ['email', false, 'Doesnt match regular expression'];
        }
        // Check email against current emails
        // todo :: add try/catch block
        $this->db->openDatabaseConnection();
        $query  = $this->db->connection->query(self::GET_ALL_EMAILS);
        $emails = $query->fetch_all(MYSQLI_ASSOC);
        for ($i = 0, $l = sizeof($emails); $i < $l; $i++) {
            // IM A GENIUS
            if ($email === $emails[ $i ]) {
                return ['email', false, 'Email already exists'];
                break;
            }
        }
        $email = mysqli_real_escape_string($this->db->connection, $email);

        return $email;
    }

    private function validatePassword($password)
    {
        // Validate
        if (trim($password) === 0 || $password === null || empty($password)
          || ! isset($password)
        ) {
            return ['password', false, 'Enter a password'];
        }
        if (strlen($password) < 8) {
            return ['password', false, 'Password must contain 8 or more characters'];
        }
        // Password - Find a number - personal algorithm
        $numberFound = false;
        while ($numberFound !== true) {
            for ($i = 0, $l = strlen($password); $i < $l; $i++) {
                $value = $password[ $i ];
                if (is_numeric($value)) {
                    $numberFound = true;
                    break;
                }
            }
            if ($numberFound !== true) {
                return ['password', 'Must contain at least one number'];
                break;
            }
        }
        if ($numberFound === true) {
            // Password - Self created algorithm - MADE IT WORK - ctype_[] didn't account for other characters than letters
            // This grabs all upper and lower case letters and then makes sure the end result contains an upper and lower
            $letterRange1    = range('a', 'z');
            $letterRange2    = range('A', 'Z');
            $passOnlyLetters = [];
            for ($i = 0, $l = strlen($password); $i < $l; $i++) {
                if (in_array($password[ $i ], $letterRange1)) {
                    array_push($passOnlyLetters, $password[ $i ]);
                }
                if (in_array($password[ $i ], $letterRange2)) {
                    array_push($passOnlyLetters, $password[ $i ]);
                }
            }
            if (ctype_upper(implode($passOnlyLetters))
              || ctype_lower(implode($passOnlyLetters))
            ) {
                return [
                  'password',
                  'Must contain at least one upper and lowercase character',
                ];
            } /* PLACE THIS SECTION IN CONTROLLER
      else {
        if ($username === $password) {
          return [
            'password',
            'Password cannot be the same as the username',
          ];
        }
        else {
          // Password
          if (strpos($password, $username) !== FALSE) {
            return ['password', 'Password cannot contain username'];
          }
      */
            else {
                if ( ! filter_var($password, FILTER_SANITIZE_STRING)) {
                    return ['password', 'Remove tags'];
                } else {
                    $this->db->openDatabaseConnection();
                    $password = mysqli_real_escape_string($this->db->connection,
                      $password);

                    return $password;
                }
            }
        }

        return false;
    }

    private
    function registerUser(
      $username,
      $email,
      $password
    ) {
        $loggedIn      = 1; // For not logged in
        $loginAttempts = 3;
        $hash          = password_hash($password, PASSWORD_BCRYPT);
        // todo :: try block
        $query = $this->db->connection->prepare(self::ADD_NEW_USER);
        $query->bind_param('sssii', $username, $email, $hash, $loggedIn,
          $loginAttempts);
        $query->execute();
        if ($query->affected_rows < 1 || $query->affected_rows > 1) {
            return ['Query did not affect the database or created more than one field'];
        }
        $this->db->closeDatabaseConnection();

        return [true];
    }

}