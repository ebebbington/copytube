<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 26/02/2019
 * Time: 11:23
 */

/**
 *  The Validate Model
 * 
 * Respoonsible for validating user input, checking
 * empty values and more
 * 
 * @author Edward Bebbington
 * @copyright
 * @license
 * @method __construct()
 * @method registerForm() The function to use when validating the registerform
 */
class ValidateModel
{
    // ///////////////////////////////////////////////////////
    // Preapred SQL Queries
    /////////////////////////////////////////////////////////
    /** @var SQL Select every user in the users table */
    const SELECT_ALL_USERS = "SELECT * FROM users";
    /** @var SQL Select all usernames in the users table by a given username */
    const GET_USERNAMES_BY_USERNAME = "SELECT username FROM users WHERE username = ?";
    /** @var SQL Select all emails in the users table by a given email */
    const GET_EMAIL_ADDRESS_BY_EMAIL = "SELECT email_address FROM users WHERE email_address = ?";

    // ///////////////////////////////////////////////////////
    // Class Properties
    /////////////////////////////////////////////////////////
    /** @var Array $usernamePolicy  Set of policies username must abide by, supplied with the rules */
    private $usernamePolicy = [
        'regEx' => "",
        'description' => []
    ];
    /** @var Array $passwordPolicy  Set of policies password must abide by, supplied with the rules */
    private $passwordPolicy = [
        'regEx' => "",
        'description' => []
    ];
    /** @var Int $commentMaxLen Maximum length a comment must abide by */
    private $commentMaxLen = 0;
    /** @var Array $result The resulting object, holding the succes, message and data of request */
    public $result = [];

    /**
     * Constructor
     * 
     * Creates the policies and rules for validation
     */
    public function __construct()
    {
        // Create Result object
        $this->result = [
            'success' => false,
            'message' => 'An error occurred',
            'data' => null
        ];
        // Set username policy
        $this->usernamePolicy['regEx'] = "/^[a-zA-Z ]{1,40}$/";
        $this->usernamePolicy['description'] = [
            'Must be at least 8 characters long',
            'Must contain at least 1 lower and uppcase character',
            'Must contain at least 1 number'
        ];
        // Set password policy
        $this->passwordPolicy['regEx'] = "/[0-9a-zA-Z]{8,}/";
        $this->passwordPolicy['description'] = [
            'Must be at least 8 characters long',
            'Must contain at least 1 lower and uppcase character',
            'Must contain at least 1 number',
            'Cannot contain username'
        ];
        // Set settings for comments
        $this->commentMaxLength = 400;
    }

    /**
     * Validate All Fields in Register Form
     * 
     * Validate the username, email and password. If the result of this object after this method
     * is true, all fields are correct
     */
    public function registerForm () {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        // Check username is set
        $this->isSet($username, 'string');
        if ($this->result['success'] === false) {
            return;
        }
        // Check email is set
        $this->isSet($email, 'string');
        if ($this->result['success'] === false) {
            return;
        }
        // Check password is set
        $this->isSet($password, 'string');
        if ($this->result['success'] === false) {
            return;
        }
        // Check they abide by the policies
        if (preg_match($this->usernamePolicy['regEx'], $username) < 1) {
            $this->result['success'] = false;
            $this->result['message'] = 'Username does not meet the policy';
            $this->result['data'] = 'username';
            return;
        }
        if (preg_match($this->passwordPolicy['regEx'], $password) < 1) {
            $this->result['success'] = false;
            $this->result['message'] = 'Password does not meet the policy';
            $this->result['data'] = 'password';
            return;
        }
        // Check username and email dont already exist
        $DatabaseModel = new DatabaseModel();
        $DatabaseModel->runQuery(self::GET_USERNAMES_BY_USERNAME, [$username]);
        if ($DatabaseModel->row) {
            $this->result['success'] = false;
            $this->result['message'] = 'Username already exists';
            $this->result['data'] = 'username';
            return;
        }
        $DatabaseModel->runQuery(self::GET_EMAIL_ADDRESS_BY_EMAIL, [$email]);
        if ($DatabaseModel->row) {
            $this->result['success'] = false;
            $this->result['message'] = 'Email already exists';
            $this->result['data'] = 'email';
            return;
        }
        // Check email is valid
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->result['success'] = false;
            $this->result['message'] = 'Email is not the required correct format';
            $this->result['data'] = 'email';
            return;
        }
        $this->result['success'] = true;
        $this->result['message'] = 'Register fields have successfully been validated, can proceed';
        $this->result['data'] = null;
    }

    /**
     * Check if a value is set
     * 
     * @param $data The value to check
     */
    private function isSet($data = null, String $type = '')
    {
        if (!$data || !isset($data) || !trim($data) || $data === null || empty($data) || gettype($data) !== $type) {
            $this->result['success'] = false;
            $this->result['message'] = 'The value is not set e.g it is empty or not the right data type';
        } else {
            $this->result['success'] = true;
            $this->result['message'] = 'All values passed in are set';
        }
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
            return $result;
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
    private function username (String $username = '')
    {
        // Check RegEx
        if (preg_match($this->usernamePolicy['regEx'], $username) < 1) {
            $this->result['success'] = false;
            $this->result['message'] = 'Username does not meet the policy';
            $this->result['data'] = 'username';
            return;
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
        $emailIsSet = $this->isSet([$email]);
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
                'message' => 'Email does not follow the policy',
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

    public function hashPassword (String $password = null) {
        if (!$password) {
            return [
                'success' => false,
                'message' => 'Password is not set when prepared for hashing',
                'data' => 'password'
            ];
        }
        $hash = password_hash($password, PASSWORD_BCRYPT);
        return [
            'success' => true,
            'message' => 'Password has been hashed',
            'data' => $hash
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
        $pos = strpos($string2, $string1);
        if ($string1 === $string2 || $pos !== false || $pos > -1) {
            return ['success' => false, 'message' => "$string1 is the same or contains $string2", 'data' => [$string1, $string2]];
        }
        return ['success' => true, 'message' => "$string1 is not the same and does not contain $string2", 'data' => [$string1, $string2]];
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
        $passwordIsSet = $this->isSet([$password]);
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
        if (preg_match('/[a-z]/', $password) !== 1 || preg_match('/[A-Z]/', $password) !== 1) {
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