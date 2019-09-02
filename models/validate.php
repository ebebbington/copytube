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
    const SELECT_ALL_USERS = "SELECT * FROM users";

    const GET_ALL_USERNAMES = "SELECT username FROM users";

    const GET_ALL_EMAILS = "SELECT email_address FROM users";

    //
    // Initialise data
    //
    private $usernameMaxLen = 40;

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

    /**
     * Validate and Sanitise a Username
     * 
     * This is for specifically validating and sanitising a username upon registering an
     * account, or for more further into the future
     * 
     * @param string $username The username field in the register form
     * @return array $result Array holding the result information of function call
     * bool ['success'] If the execution followed through as expected
     * string ['message'] Message to be accompanied with the success
     * any ['data'] data to be passed back containing needed information
     */
    public function validateUsername($username = '')
    {
        // Validate
        if (strlen($username) > $this->usernameMaxLen || trim($username) === 0
          || $username === null
          || empty($username)
          || ! isset($username)
        ) {
            return ['success' => false, 'message' => 'Username is either empty or too long', 'data' => 'username'];
        }
        // Check RegEx
        if (preg_match("/^[a-zA-Z ]*$/", $username) !== 1) {
            return ['success' => false, 'message' => 'Only letters and whitespaces are allowed', 'data' => 'username'];
        }
        // Sanitise
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        if (!$username) {
            return ['success' => false, 'message' => 'Tags are not allowed', 'data' => 'username'];
        }
        // Check if username exists
        $db = new Database();
        $result = $db->runQuery(self::GET_ALL_USERNAMES)
        if ($result['success'] === false) {
            return $result;
        }
        $usernames = $result['data'];
        for ($i = 0, $l = sizeof($usernames); $i < $l; $i++) {
            // IM A GENIUS
            if ($username === $usernames[ $i ]) {
                return [
                    'success' => false,
                    'message' => 'Username already exists',
                    'data' => 'username'
                ];
                break;
            }
        }
        return [
            'success' => true,
            'message' => 'Username successfuly validated',
            'data' => $username
        ];
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

    public function validateEmail($email)
    {
        // Validate
        if (trim($email) === 0 || $email === null || empty($email)) {
            return [
                'success' => false,
                'message' => 'Enter a correct format',
                'data' => 'email'
            ];
        }
        // Sanitise
        // todo :: use FILTER_VALIDATE_EMAIL too
        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            return [
                'success' => false,
                'message' => 'Emaildoes not follow the policy',
                'data' => 'email'
            ];
        }
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (!$email) {
            return [
                'success' => false,
                'message' => 'Remove tags',
                'data' => 'email'
            ];
        }
        // RegEx
        // todo :: find a working email regex
        // if (preg_match("[a-zA-Z]+@[a-zA-Z]+\.[a-zA-Z]{2,}", $email) !== 1) {
        //     return [
        //         'success' => false,
        //         'message' => 'Doesnt match expression',
        //         'data' => 'email'
        //     ];
        // }
        // Check email against current emails
        try {
            // todo :: getting all emails is so inefficient smh... just get them WHERE email = ? you pleb
            $this->db->openDatabaseConnection();
            $query  = $this->db->connection->query(self::GET_ALL_EMAILS);
            $emails = $query->fetch_all(MYSQLI_ASSOC);
            for ($i = 0, $l = sizeof($emails); $i < $l; $i++) {
                $existingEmail = $emails[1]['email_address'];
                // IM A GENIUS
                if ($email === $existingEmail) {
                    return [
                        'success' => false,
                        'message' => 'Email already exists',
                        'data' => 'email'
                    ];
                    break;
                }
            }
        } catch (Exception $e) { var_dump($e); };
        $email = mysqli_real_escape_string($this->db->connection, $email);

        return [
            'success' => true,
            'message' => 'Scuessfully validated email',
            'data' => $email
        ];
    }

    public function compareStrings ($string1 = '', $string2 = '') {
        if (!$string1 || !$string2) {
            return true;
        }
        if ($string1 === $string2 || strpos($string2, $string1) !== false) {
            return true;
        }
        return false;
    }

    public function validatePassword($password)
    {
        // Validate
        if (trim($password) === 0 || $password === null || empty($password)
          || ! isset($password)
        ) {
            return [
                'success' => false,
                'message' => 'Enter a password',
                'data' => 'password'
            ];
        }
        if (strlen($password) < 8) {
            return [
                'success' => false,
                'message' => 'Password must cotain 8 or more characters',
                'data' => 'password'
            ];
        }
        // Password - Find a number - personal algorithm
        $numberFound = false;
        for ($i = 0, $l = strlen($password); $i < $l; $i++) {
            $value = $password[ $i ];
            if (is_numeric($value)) {
                $numberFound = true;
                break;
            }
        }
        if ($numberFound !== true) {
            return [
                'success' => false,
                'message' => 'Password must contain at least 1 number',
                'data' => 'password'
            ];
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
                    'success' => false,
                    'message' => 'Must contain at least 1 or more uppercase and lowercase letters',
                    'data' => 'password'
                ];
            }
            if ( ! filter_var($password, FILTER_SANITIZE_STRING)) {
                return [
                    'success' => false,
                    'message' => 'Remove tags',
                    'data' => 'password'
                ];
            } else {
                $this->db->openDatabaseConnection();
                $password = mysqli_real_escape_string($this->db->connection,
                    $password);
                $this->db->closeDatabaseConnection();
                return [
                    'success' => true,
                    'message' => 'Successfully validated',
                    'data' => $password
                ];
            }
        }
        return [
            'success' => false,
            'message' => 'Please enter at least one number',
            'data' => 'password'
        ];
    }
}