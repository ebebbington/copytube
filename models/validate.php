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
    const GET_USERNAMES_BY_USERNAME = "SELECT username FROM users WHERE username = ?";
    const GET_EMAILS_BY_EMAIL = "SELECT email_address FROM users WHERE email_address = ?";

    //
    // Initialise data
    //
    private $usernameMaxLen = 40;
    private $commentMaxLen = 400;
    private $usernames;
    private $passwordMinLen = 8;

    //
    // Initialise
    //
    public function __construct()
    {
    }

    /**
     * Are a list of values set and valid
     * 
     * @param Array $data The arra to loop through checking each value
     * @return Array [success, message, data] The response object
     */
    public function isSet(Array $data = [])
    {
        for ($i = 0; $i < (sizeof($data) - 1); $i++) {
            if ( ! $data[ $i ] || ! isset($data[ $i ]) || trim($data[$i] < 1 || $data[$i] === null || empty($data[$i] ))) {
                return [
                    'success' => false,
                    'message' => 'Some values are not set or dont contain anything',
                    'data' => null
                ];
                break;
            }
        }
        return [
            'success' => true,
            'message' => 'Values are set',
            'data' => $data
        ];
    }

    /**
     * Prepare a string to display by sanitising it
     * 
     * @param String $value The string to check
     * @return Array [success, message, data] The response object
     */
    public function prepareStringForView (String $value = '') {
        $value = filter_var($string, FILTER_SANITIZE_STRING);
        if (!$value) {
            return ['success' => false, 'message' => 'Tags are not allowed', 'data' => null];
        }
        return = ['success' => true, 'message' => 'Sanitised the input', 'data' => $value];
    }

    /**
     * Validate a comment
     * 
     * @param String $comment The comment the user added
     * @return Array $result[success, message, data] The response object
     */
    public function comment($comment)
    {
        $result = [
            'success' => false,
            'message' => '',
            'data' => 'comment'
        ];
        // Validate
        $commentIsSet = $this->isSet($comment);
        if ($commentIsSet['success'] === false) {
            $result['message'] = 'Comment is not a valid string';
            return $result;
        }
        if (strlen($comment) > $this->commentMaxLen) {
            $result['message'] = 'Comment is too long';
            return $result
        }
        $result['success'] = true;
        $result['message'] = 'Validated comment';
        $result['data'] = $comment;
        return $result;
    }


    /**
     * Validate and Sanitise a Username
     * 
     * This is for specifically validating and sanitising a username upon registering an
     * account, or for more further into the future
     * 
     * @param string $username The username field in the register form
     * @return Array [success, message, data] The response object
     */
    public function validateUsername(String $username = '')
    {
        // Validate
        $usernameIsSet = $this->isSet($username);
        if (!$usernameIsSet) {
            return ['success' => false, 'message' => 'Username is not set', 'data' => 'username'];
        }
        if (strlen($username) > $this->usernameMaxLen) {
            return ['success' => false, 'message' => 'Username is too long', 'data' => 'username'];
        }
        // Check RegEx
        if (preg_match("/^[a-zA-Z ]*$/", $username) < 1) {
            return ['success' => false, 'message' => 'Only letters and whitespaces are allowed', 'data' => 'username'];
        }
        // Sanitise
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        if (!$username) {
            return ['success' => false, 'message' => 'Tags are not allowed', 'data' => 'username'];
        }
        // Check if username exists
        $db = new Database();
        $result = $db->runQuery(self::GET_USERNAMES_BY_USERNAME, [$username]);
        if ($result['success'] === false) {
            return $result;
        }
        if ($result['rowCount'] === 1) { // username already exists
            return ['success' => false, 'message' => 'Username already exists', 'data' => 'username'];
        }
        /* Old way before i added in a username param to the query
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
        } */
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
            } catch (exception $e
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

    /**
     * Validate and Sanitise an Email
     * 
     * @param string $email The email string to check
     * @return array $result Array holding the result information of function call
     * bool ['success'] If the execution followed through as expected
     * string ['message'] Message to be accompanied with the success
     * any ['data'] data to be passed back containing needed information
     */
    public function validateEmail(String $email = '')
    {
        $emailIsSet = $this->isSet($email);
        if ($email['success'] === false) {
            return [
                'success' => false,
                'message' => 'Email cannot be empty or not set',
                'data' => 'email'
            ];
        }
        // Sanitise
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
        // Check email against current emails
        $db = new Database();
        $result = $db->runQuery(self::GET_EMAILS_BY_EMAIL, [$email]);
        if ($result['success'] === false) {
            return $result;
        }
        if ($result['rowCount'] === 1) {
            return [
                'success' => false,
                'message' => 'Email already exists',
                'data' => 'email'
            ];
        }
        return [
            'success' => true,
            'message' => 'Scuessfully validated email',
            'data' => $email
        ];
    }

    /**
     * Check if 2 strings are the same or if one contains the other
     * 
     * @param String $string1 The first string to use
     * @param String $string2 The second string to compare against
     * @return Array The resulting object
     */
    public function compareStrings (String $string1 = '', String $string2 = '') {
        if ($string1 === $string2 || strpos($string2, $string1) !== false) {
            return ['success' => true, 'message' => "$string1 is the same or contains $string2", 'data' => [$string1, $string2]];
        }
        return ['success' => false, 'message' => "$string1 is not the same and does not contain $string2", 'data' => [$string1, $string2]];
    }

    /**
     * Validate a password
     * 
     * Make checks against the password
     * 
     * @param String $password The password to check
     * @return Array The resulting object
     */
    public function validatePassword(String $password = '')
    {
        // Validate
        $passwordIsSet = $this->isSet($password);
        if ($passwordIsSet['success'] === false) {
            return [
                'success' => false,
                'message' => 'Enter a password',
                'data' => 'password'
            ];
        }
        if (strlen($password) < $this->passwordMinLen) {
            return [
                'success' => false,
                'message' => 'Password must cotain 8 or more characters',
                'data' => 'password'
            ];
        }
        // Find a number
        if (preg_match('~[0-9]~', $password) < 1) {
            return [
                'success' => false,
                'message' => 'Password must contain at least 1 number',
                'data' => 'password'
            ];
        }
        // Find upper and lower case
        if (preg_match('/[a-z][A-Z]/', $password) < 1) {
            return [
                'success' => false,
                'message' => 'Must contain at least 1 or more uppercase and lowercase letters',
                'data' => 'password'
            ];
        }
        /* Old way before I solved the whole number situation with a single line... smh
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
        }*/
        return [
            'success' => true,
            'message' => 'Password successfully validated',
            'data' => $password
        ];
    }
}