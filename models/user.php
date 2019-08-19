<?php
//session_save_path('/tmp');
session_start();
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:47
 */
//
// Error Handler - Log them
//

set_error_handler(function ($code, $text, $file, $line, $content) {
  $fileSize = (filesize('../../data/error.txt') / 1000) / 1000; // in megabytes
  $fileSize > 10 ? $writeType = 'w' : $writeType = 'a';
  $errorArray   = ["\nError: $code", "\nDescription: $text", "\nFile with error: $file", "\nLine: $line"];
  $errorLogFile = fopen('../../data/error.txt', $writeType);
  for ($i = 0; $i < sizeof($errorArray); $i++) {
    fwrite($errorLogFile, $errorArray[ $i ]);
  }
  fclose($errorLogFile);

  return TRUE;
}, E_ALL | E_STRICT);

include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/controllers/database.php';
include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/validate.php';

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

class User {
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
  private $user;
  private $userId;

  //
  // Initialise Data
  //
  public function __construct () {
    $this->db = new Database();
    $this->db->openDatabaseConnection(); // DOES OPEN THE CONNECTION WITHOUT ANY OTHER LINES OF CODE AND CAN CLOSE FURTHER DOWN THE LINE
    $this->validate = new Validate();
  }

  public function checkSession () {
    if (empty($_COOKIE[ 'sessionId1' ]) || empty($_COOKIE[ 'sessionId2' ])) {
      // Divert back to login and remove all cookies
      $this->logout();

      return FALSE;
      //return ['login', 'User is not logged in'];
    } else {
      return TRUE;
    }
  }

  //
  // Generate API Key
  //
  private function generateKey () {
    try {
      $key = bin2hex(random_bytes(32));

      return $key;
    } catch (exception $e) {
      return ['login', FALSE, 'Could not generate an API key'];
    }
  }

  //
  // Get API Key
  //
  public function getKey () {
    if (isset($_SESSION[ 'key' ])) {
      $userKey = [$_SESSION[ 'key' ][ 0 ], $_SESSION[ 'key' ][ 1 ]];

      return json_encode($userKey);
    }

    return json_encode(['key', 'Session key is not set']);
  }

  //
  // Save API Key
  //
  private function saveKey ($key, $uid) {
    // Save key in memory
    $userKey           = [$uid, $key];
    $_SESSION[ 'key' ] = $userKey;
    // Save to API
    $apiUrl    = 'http://localhost:3003/keys';
    $curl      = curl_init($apiUrl);
    $data      = new stdClass();
    $data->uid = $uid;
    $data->key = $key;
    $json      = json_encode($data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    curl_exec($curl);
    curl_close($curl);
  }

  public function deleteKey () {
    $key = $_SESSION[ 'key' ][ 1 ];
    unset($_SESSION[ 'key' ]);
    $apiUrl    = 'http://localhost:3003/keys';
    $curl      = curl_init($apiUrl);
    $data      = new stdClass();
    $data->key = $key;
    $json      = json_encode($data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    curl_exec($curl);
    curl_close($curl);
  }

  //
  // Get User
  //
  public function getUser () {
    // ///////////////////////////////////////////////////////
    // Note: User can now be grabbed using $_SERVER['user'] //
    // Using the session object is a more efficient way of  //
    // getting the user data needed                         //
    // ///////////////////////////////////////////////////////
    if ( ! isset($_COOKIE[ 'sessionId2' ])) {
      return FALSE;
    }
    $sessionId2 = $_COOKIE[ 'sessionId2' ];
    try {
      // Get user session data using the session id
      $query = $this->db->connection->prepare(self::GET_USER_ID);
      $query->bind_param('s', $sessionId2);
      $query->execute();
      $this->user   = $query->get_result()->fetch_all(MYSQLI_ASSOC);
      $this->userId = $this->user[ 0 ][ 'user_id' ];
      // Use the user id in the session id to grab the user
      $query = $this->db->connection->prepare("SELECT * FROM users WHERE id = $this->userId");
      $query->execute();
      $this->user = $query->get_result()->fetch_all(MYSQLI_ASSOC);
      unset($this->user[ 0 ][ 'password' ]);
    } catch (error $e) {
      // todo add content
      return FALSE;
    } finally {
      return $this->user;
    }
  }

  //
  // Create Cookies
  //
  private function createCookies () {
    $sessionId1 = random_bytes(16);
    $sessionId1 = bin2hex($sessionId1);
    $sessionId2 = random_bytes(16);
    $sessionId2 = bin2hex($sessionId2);

    return [$sessionId1, $sessionId2];
  }
  //
  // Set Cookies
  //
  private function setCookies ($cookies, $id) {
    setcookie('sessionId1', $cookies[ 0 ], time() + 3200, '/');
    setcookie('sessionId2', $cookies[ 1 ], NULL, '/');
    $this->db->connection = new mysqli('192.168.56.101', 'root', 'JahRastafarI1298', 'copytube');
    $query                = $this->db->connection->prepare(self::INSERT_NEW_SESSION);
    $query->bind_param('ssi', $cookies[ 0 ], $cookies[ 1 ], $id);
    $query->execute();
  }
  //
  // Unset cookies
  //
  private function unsetCookies () {
    setcookie("sessionId1", "", time() - 3600, '/');
    setcookie('PHPSESSID', '', time() - 3600, '/');
    setcookie("sessionId2", "", time() - 3600, '/');
    setcookie("name", "", time() - 3600, '/');
  }

  //
  // Run Login Function
  //
  public function login ($postData) {
    $emailInput    = $postData[ 'email' ];
    $passwordInput = $postData[ 'password' ];
    $user          = [];
    try {
      $query = $this->db->connection->prepare(self::GET_CURRENT_USER);
      $query->bind_param('s', $emailInput);
      $query->execute();
      $query->bind_result($user[ 0 ][ 'id' ], $user[ 0 ][ 'username' ], $user[ 0 ][ 'email' ], $user[ 0 ][ 'password' ],
        $user[ 0 ][ 'loggedIn' ], $user[ 0 ][ 'loginAttempts' ]);
      $query->fetch(); // This is needed, otherwise if i try to access the binded variables the output is ""
      if ( ! $user[ 0 ][ 'id' ]) {
        // Means ive used the wrong email
      }
    } catch (error $e) {
      // todo handle me
    } finally {
      // Means correct email is given
      // fixme the if condition is false? correct pass is given...
      if (password_verify($passwordInput, $user[ 0 ][ 'password' ])) {
        if ($user[ 0 ][ 'loginAttempts' ] === 0) {
          $this->lockoutEmail($postData);

          return json_encode(['lockout', TRUE]);
        } else {
          // Create the cookies
          $cookies = $this->createCookies();
          // Set in global var and save to db
          $this->setCookies($cookies, $user[ 0 ][ 'id' ]);
          // Update loggedIn
          $query = $this->db->connection->prepare(self::SET_LOGGED_IN);
          $query->bind_param('s', $user[ 0 ][ 'email' ]);
          $query->execute();
          unset($user[ 0 ][ 'password' ]);
          $_SESSION[ 'user' ] = $user;
          $key                = $this->generateKey();
          if (is_array($key)) {
            return json_encode($key);
          } else {
            $this->saveKey($key, $user[ 0 ][ 'id' ]);

            return json_encode(['login', TRUE]);
          }
        }
      } else {
        // Password not the same
        try {
          $this->db->openDatabaseConnection();
          //$this->db->connection = new mysqli('localhost', 'root', 'password', 'copytube');
          $query         = $this->db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
          $loginAttempts = $user[ 0 ][ 'loginAttempts' ] - 1;
          $query->bind_param('is', $loginAttempts, $emailInput);
          $query->execute();
        } catch (error $e) {
          // todo handle error
          var_dump($e);
        } finally {
          return FALSE;
        }
      }
    }
  }

  //
  // Run Logout function
  //
  public function logout () {
    if (isset($_COOKIE[ 'sessionId2' ])) {
      $sessionId = $_COOKIE[ 'sessionId2' ];
      $this->db->openDatabaseConnection();
      $query = $this->db->connection->prepare(self::GET_USER_ID);
      $query->bind_param('s', $sessionId);
      $query->execute();
      $user = [];
      $query->bind_result($user[ 0 ][ 'user_id' ]);
      $query->fetch(); // This is needed, otherwise if i try to access the binded variables the output is ""
      $userId               = $user[ 0 ][ 'user_id' ];
      $this->db->connection = new mysqli('localhost', 'root', 'password', 'copytube');
      $query                = $this->db->connection->prepare(self::LOGOUT_USER);
      $query->bind_param('i', $userId);
      $query->execute();
      $query = $this->db->connection->prepare(self::DELETE_SESSION);
      $query->bind_param('i', $userId);
      $query->execute();
      $this->deleteKey();
      $this->unsetCookies();
      session_abort();
      session_unset();
    }

    return FALSE;
  }

  //
  // Run Register function
  //
  public function register ($postData) {
    return $this->validate->validateUsername($postData);
  }

  //
  // Tell user Account is Locked
  //
  private function lockoutEmail ($postData) {
    $receiver = $postData[ 'email' ];
    $subject  = 'Account Locked Out';
    $message
              = "Your account $receiver has been locked out on CopyTube. To recover it please visit http://localhost/copytube/public/view/recover.html";
    $header   = 'From: noreply@copytube.com';
    mail($receiver, $subject, $message, $header);
  }

  //
  // Tell user Account is Recovered
  //
  private function recoverEmail ($postData) {
    $receiver = $postData[ 'email' ];
    $subject  = 'Account Recovered';
    $message  = "Your account $receiver has been recovered on CopyTube.";
    $header   = 'From: noreply@copytube.com';
    mail($receiver, $subject, $message, $header);
  }

  //
  // Run Recover function
  //
  public function recover ($postData) {
    $email    = $postData[ 'email' ];
    $password = $postData[ 'password' ];
    $query    = $this->db->connection->prepare(self::GET_CURRENT_USER);
    $query->bind_param('s', $email);
    $query->execute();
    $user = [];
    $query->bind_result($user[ 0 ][ 'id' ], $user[ 0 ][ 'username' ], $user[ 0 ][ 'email' ], $user[ 0 ][ 'password' ],
      $user[ 0 ][ 'loggedIn' ], $user[ 0 ][ 'loginAttempts' ]);
    $query->fetch();
    if ($user[ 0 ][ 'id' ] === NULL) {
      // User doesn't exist
      print_r(json_encode(['email', FALSE]));
    } else {
      // Validate
      if ($user[ 0 ][ 'loginAttempts' ] === 0) {
        if (password_verify($password, $user[ 0 ][ 'password' ])) {
          $this->db->connection = new mysqli('localhost', 'root', 'password', 'copytube');
          $query                = $this->db->connection->prepare(self::UPDATE_LOGIN_ATTEMPTS);
          $loginAttempts        = 3;
          $query->bind_param('is', $loginAttempts, $email);
          $query->execute();
          $this->recoverEmail($postData);
          print_r(json_encode(['password', TRUE]));
        } else {
          print_r(json_encode(['password', FALSE]));
        }
      } else {
        // User should not be recovering as it's already recovered
        print_r(json_encode(['email', 'No recovering is needed']));
      }
    }
    $this->db->closeDatabaseConnection();
  }

}