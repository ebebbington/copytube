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
  $configPath = $_SERVER['DOCUMENT_ROOT']. '/config/copytube.ini';
  $config = parse_ini_file($configPath, true);
  $errorLogPath = $config['Logging']['error_log_file'];
  $fileSize = (filesize($errorLogPath) / 1000) / 1000; // in megabytes
  $fileSize > 10 ? $writeType = 'w' : $writeType = 'a';
  $errorArray   = ["\nError: $code", "\nDescription: $text", "\nFile with error: $file", "\nLine: $line"];
  $errorLogFile = fopen($errorLogPath, $writeType);
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
  const GET_USER_ID_BY_SESSIONID2 = "SELECT user_id FROM sessions WHERE session_id_2 = ? LIMIT 1";

  const GET_USER_BY_USER_ID = "SELECT * FROM users WHERE id = ? LIMIT 1";

  const LOG_USER_OUT_BY_ID = "UPDATE users SET logged_in = 1 WHERE id = ?";

  const DELETE_SESSION_BY_USER_ID = "DELETE FROM sessions WHERE user_id = ?";

  const GET_USER_BY_EMAIL = "SELECT * FROM users WHERE email_address = ? LIMIT 1";

  const CREATE_SESSION = "INSERT INTO sessions (session_id_1, session_id_2, user_id) VALUES (?, ?, ?)";

  const UPDATE_LOGIN_ATTEMPTS = "UPDATE users SET login_attempts = ? WHERE email_address = ?";

  const GET_ALL_USERS = "SELECT * FROM users";

  const LOG_USER_IN_BY_ID = "UPDATE users SET logged_in = 0 WHERE id = ?";

  const CREATE_USER = "INSERT INTO users (username, email_address, password, logged_in, login_attempts) VALUES (?, ?, ?, ?, ?)";

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
  private $maxLoginAttempts = 3;

  //
  // Initialise Data
  //
  public function __construct () {
  }

  /**
   * Checks the Cookies are Set
   * 
   * Checks if sessionId1 and sessionId2 cookies are set and valid
   * 
   * @return Array [success, message, data] The resulting object
   */
  public function areCookiesSet () {
    $cookies = [$_COOKIE['sessionId1'], $sessionId2 = $_COOKIE['sessionId2']];
    for ($i = 0, $l = (sizeof($cookies) - 1); $i < $l; $i++) {
      $c = $cookies[$i];
      if ($c === null || empty($c) || !isset($c) || !$c) {
        return [
          'success' => false,
          'message' => "A require cookie is not set",
          'data' => null
        ];
        break;
      }
    }
    return [
      'success' => true,
      'message' => "Cookies are set",
      'data' => null
    ];
  }

  /**
   * Generate an API Key
   * 
   * Generate a key for an API, specifically the one i created
   * 
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  private function generateKeyForAPI () {
    try {
      $key = bin2hex(random_bytes(32));
      return ['success' => true, 'message' => 'Created the API key', 'data' => $key];
    } catch (Exception $e) {
      return ['success', 'message' => 'Failed to generate an API key', 'data' => false];
    }
  }

  /**
   * Save the API key
   * 
   * Save the API key that can be generated into the session object
   * 
   * @param Int $key The key to be used in the API request
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function saveAPIKey (Int $key = null) {
    if (!$key) {
      return [
        'success' => false,
        'message' => 'No API key has been set',
        'data' => null
      ];
    }
    $_SESSION['APIKey'] = $key;
    return [
      'success' => true,
      'message' => 'The API key has been saved into the session object',
      'data' => null
    ];
  }

  /**
   * Get the API key from the session object
   * 
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function getAPIKey () {
    if (isset($_SESSION[ 'APIKey' ])) {
      return [
        'success' => true,
        'message' => 'Got the API key',
        'data' => $_SESSION['APIKey']
      ];
    }
    return [
      'success' => false,
      'message' => 'Could not find the API Key',
      'data' => null
    ];
  }

  // COME BACK TO ME, I NEED RETHINKING
  public function sendAPIKey ($uid) {
    $result = $this->getAPIKey();
    if ($result['success'] === false) {
      return [
        'success' => false,
        'message' => 'Could not grab the API key to be sent',
        'data' => null
      ];
    }
    $key = $result['data'];
    // todo :: get userID
    $userId = 0;
    // Save key in memory
    // $userKey           = [$uid, $key];
    // $_SESSION[ 'key' ] = $userKey;
    // Save to API
    $APIUrl    = 'http://localhost:3003/keys';
    $curl      = curl_init($apiUrl);
    $data      = new stdClass();
    $data->uid = $userId;
    $data->key = $key();
    $json      = json_encode($data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    curl_exec($curl);
    curl_close($curl);
  }

  // COME BACK TO ME I NEED ETHINKING
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

  /**
   * Check a login password against a password in the database matching the email
   * 
   * @param String $rawPassword The raw password
   * @param String $passwordHash The password taken from the database
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function doPasswordsMatch (String $rawPassword = null, String $passwordHash = null) {
    if (!$password || !$passwordHash) {
      return [
        'success' => false,
        'message' => 'One of the parameters is not set',
        'data' => null
      ];
    }
    if (!password_verify($password, $passwordHash)) {
      return [
        'success' => false,
        'message' => 'Passwords do not match',
        'data' => null
      ];
    }
    return [
      'success' => true,
      'message' => 'Passwords match',
      'data' => null
    ];
  }

  /**
   * Save the User Object into te Session object
   * 
   * @param Array $userData The array holding all details about the user. The password is removed here too
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function saveUserInSession (Array $userData = null) {
    if (!$userData) {
      return [
        'success' => false,
        'message' => 'No data is inside the parameter',
        'data' => null
      ];
    }
    if ($_SESSION['user']) {
      $_SESSION['user'] = null;
    }
    unset($userData[0]['password']);
    $_SESSION['user'] = $userData;
    return [
      'success' => true,
      'message' => 'Saved the user object into the session',
      'data' => null
    ];
  }

  /**
   * Find a User
   * 
   * Find an account in the database with the email
   * 
   * @param String $email Used to look for a user
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function getUserByEmail (String $email = '') {
    $db = new Database();
    $result = $db->runQuery(self::GET_USER_BY_EMAIL, [$email]);
    if ($result['success'] === false) {
      return $result;
    }
    return [
      'success' => true,
      'message' => 'Found a user with that email',
      'data' => $result['data']
    ];
  }

  /**
   * Get all user data from the database using the sessionId2
   * 
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function getUserBySessionId2 () {
    if ( ! isset($_COOKIE[ 'sessionId2' ])) {
      return [
        'success' => false,
        'message' => 'Sessionid2 is not set',
        'data' => null
      ];
    }
    $sessionId2 = $_COOKIE[ 'sessionId2' ];
    $db = new Database();
    $result = $db->runQuery(self::GET_USER_ID_BY_SESSIONID2, [$sessionId2]);
    if ($result['success'] === false) {
      return $result;
    }
    $result = $db->runQuery(self::GET_USER_BY_USER_ID, [$result['data'][0]['id']]);
    if ($result['success'] === false) {
      return $result;
    }
    return [
      'success' => true,
      'message' => 'Got the user from the database using the session id 2';
      'data' => $result['data'][0]
    ];
  }

  /**
   * Create the session cookies and save them into the session
   * 
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  private function createAndSaveSessionCookies () {
    // create
    $sessionId1 = random_bytes(16);
    $sessionId1 = bin2hex($sessionId1);
    $sessionId2 = random_bytes(16);
    $sessionId2 = bin2hex($sessionId2);
    // save in session object
    setcookie('sessionId1', $sessionId1, time() + 3200, '/');
    setcookie('sessionId2', $sessionId2, NULL, '/');
    // save to the database
    $userId = $_SESSION['user']['id'];
    if (!$userId) {
      return [
        'success' => false,
        'message' => 'User id in session is not set',
        'data' => null
      ];
    }
    $db = new Database();
    $db->runQuery(self::CREATE_SESSION, [$sessionId1, $sessionId2, $userId]);
    if ($result['success'] === false) {
      return $result;
    }
    return [
      'success' => true,
      'message' => 'Saved session to the database and to the session object',
      'data' => null
    ];
  }
  
  /**
   * Remove all cookies created by this application
   * 
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  private function removeAllCookies () {
    setcookie("sessionId1", "", time() - 3600, '/');
    setcookie('PHPSESSID', '', time() - 3600, '/');
    setcookie("sessionId2", "", time() - 3600, '/');
    setcookie("name", "", time() - 3600, '/');
    return [
      'success' => true,
      'message' => 'Removed all cookies',
      'data' => null
    ];
  }

  /**
   * Check if a users account has been locked before logging them in
   * 
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function isAccountLocked () {
    $db = new Database();
    $dbResult = $db->runQuery(self::GET_USER_BY_USER_ID, [$_SESSION['user']['id']]);
    if ($dbResult['success'] === false) {
      return $result;
    }
    if ($dbResult['data'][0]['login_attempts'] =< 0) {
      return [
        'success' => false,
        'message' => 'Account is locked',
        'data' => null
      ];
    }
    return [
      'success' => true,
      'message' => 'User account is not locked',
      'data' => $dbResult['data'][0]['login_attempts']
    ];
  }

  /**
   * Update the login attempts of the field if an incorrect login is given against the email
   *
   * @param String $email Email to educe the login attempts against
   * @param Int $loginAttempts The current number of login attempts against the account
   * @return Array [
   *  success => if the action worked,
   *  message => message of the failed or successful action,
   *  data => any data to be passed back
   * ]
   */
  public function decreaseLoginAttempts (String $email = null, Int $login_attempts = null) {
    $db = new Database();
    $loginAttempts = $loginAttempts - 1;
    $dbResult = $db->runQuery(self::UPDATE_LOGGED_IN, [$loginAttempts, $email]);
    if ($dbResult['success'] === false) {
      return $result;
    }
    return [
      'success' => true,
      'message' => 'Login attempts have been updated',
      'data' => null
    ];
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
  public function register ($username = '', $email = '', $password = '') {
    $loggedIn      = 1; // For not logged in
    $loginAttempts = 3;
    $hash          = password_hash($password, PASSWORD_BCRYPT);
    try {
      $this->db->openDatabaseConnection();
      $query = $this->db->connection->prepare(self::ADD_NEW_USER);
      $query->bind_param('sssii', $username, $email, $hash, $loggedIn, $loginAttempts);
      $query->execute();
      $this->db->closeDatabaseConnection();
      if ($query->affected_rows < 1 || $query->affected_rows > 1) {
        return [
          'success' => false,
          'message' => 'There was a problem creating an account',
          'data' => 'register'
        ];
      }
      return [
        'success' => true,
        'message' => 'Account successfully created',
        'data' => 'register'
      ];
    } catch (Exception $e) {
      var_dump($e);
    } 
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
  private function sendRecoverEmail (Array $data = null) {
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