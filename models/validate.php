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

class Validate {

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

  //
  // Initialise
  //
  public function __construct () {
    $this->db = new Database();
  }

  public function validateComment ($comment) {
    // Validate
    if (strlen($comment) > 400 || trim($comment) === 0 || $comment === NULL
      || empty($comment)
      || ! isset($comment)
    ) {
      return FALSE;
    }
    // Sanitise
    if ( ! filter_var($comment, FILTER_SANITIZE_STRING)) {
      return FALSE;
    }
    // Return
    $this->db->openDatabaseConnection();
    $comment = mysqli_real_escape_string($this->db->connection, $comment);
    $this->db->closeDatabaseConnection();

    return $comment;
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
  public function validateUsername ($username) {
    // Validate
    if (strlen($username) > $this->maxlength || trim($username) === 0
      || $username === NULL
      || empty($username)
      || ! isset($username)
    ) {
      return ['username', 'Enter a Username'];
    }
    // Check RegEx
    if ( ! preg_match('/^[a-zA-Z ]*$/', $username)) {
      return ['username', 'Only letters and whitespaces allowed'];
    }
    // Sanitise
    if ( ! filter_var($username, FILTER_SANITIZE_STRING)) {
      return ['username', 'Remove tags'];
    }
    // Check if username exists
    // todo :: add try/catch block
    $this->db->openDatabaseConnection();
    $query     = $this->db->connection->query(self::GET_ALL_USERNAMES);
    $usernames = $query->fetch_all(MYSQLI_ASSOC);
    $this->db->closeDatabaseConnection();
    for ($i = 0, $l = sizeof($usernames); $i < $l; $i++) {
      // IM A GENIUS
      if ($username === $usernames[ $i ]) {
        return ['username', 'Username already exists'];
        break;
      }
    }
    $username = mysqli_real_escape_string($this->db->connection, $username);

    return $username;
  }

  private function verifyEmail (
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
        return TRUE;
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

  private function validateEmail ($email) {
    // Validate
    if (trim($email) === 0 || $email === NULL || empty($email)
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
      return ['email', FALSE, 'Doesnt match regular expression'];
    }
    // Check email against current emails
    // todo :: add try/catch block
    $this->db->openDatabaseConnection();
    $query  = $this->db->connection->query(self::GET_ALL_EMAILS);
    $emails = $query->fetch_all(MYSQLI_ASSOC);
    for ($i = 0, $l = sizeof($emails); $i < $l; $i++) {
      // IM A GENIUS
      if ($email === $emails[ $i ]) {
        return ['email', FALSE, 'Email already exists'];
        break;
      }
    }
    $email = mysqli_real_escape_string($this->db->connection, $email);

    return $email;
  }

  private function validatePassword ($password) {
    // Validate
    if (trim($password) === 0 || $password === NULL || empty($password)
      || ! isset($password)
    ) {
      return ['password', FALSE, 'Enter a password'];
    }
    if (strlen($password) < 8) {
      return ['password', FALSE, 'Password must contain 8 or more characters'];
    }
    // Password - Find a number - personal algorithm
    $numberFound = FALSE;
    while ($numberFound !== TRUE) {
      for ($i = 0, $l = strlen($password); $i < $l; $i++) {
        $value = $password[ $i ];
        if (is_numeric($value)) {
          $numberFound = TRUE;
          break;
        }
      }
      if ($numberFound !== TRUE) {
        return ['password', 'Must contain at least one number'];
        break;
      }
    }
    if ($numberFound === TRUE) {
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

    return FALSE;
  }

  private
  function registerUser (
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

    return [TRUE];
  }

}