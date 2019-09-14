<?php

/**
 *  The Register Controller
 * 
 * Handles requests to the /register endpoint, utilising
 * the ResponseController to return a response to the
 * view
 * 
 * @author Edward Bebbington
 * @copyright
 * @license
 * @method __construct()
 * @method runRequest()
 * @method post()
 * @method get()
 */
 class RegisterController extends ResponseController {

    // ///////////////////////////////////////////////////////
    // Preapred SQL Queries
    /////////////////////////////////////////////////////////
    /** @var SQL Insert a new user in the database */
    const CREATE_USER = "INSERT INTO users (username, email_address, password, logged_in, login_attempts) VALUES (?, ?, ?, ?, ?)";

    // ///////////////////////////////////////////////////////
    // Class Properties
    /////////////////////////////////////////////////////////
    /** @var String $username   Should contain username on a POST */
    private $username = '';
    /** @var String $email      Should contain email on a POST */
    private $email = '';
    /** @var String $password   Should contain password on a POST */
    private $password = '';
    /** @var String $method     The request method */
    private $method = '';
    /** @var Array $request     Contains mainly if the request is an AJAX */
    private $request  = [];
    /** @var String $isAjax     If set the request is AJAX */
    private $isAjax = '';
    /** @var String $viewPath   The path to the view for register */
    private $viewPath = '';

    /**
     * Constructor
     * 
     * Assign core pieces to the controller
     */
    public function __construct () {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->isAjax = $_REQUEST['ajax'];
        $this->viewPath = '/views/register.html';
        $this->runRequest();
    }

    /**
     * Run the Desired Request
     * 
     * Whether its an AJAX, or HTTP GET, run the
     * requested action
     */
    private function runRequest () {
        // Check if an AJAX request
        if ($this->isAjax) {
            $this->post();
        }
        // Check if GET HTTP Request
        if (!$this->isAjax && $this->method === 'GET') {
            $this->get();
        }
    }

    /**
     * AJAX Post Request
     * 
     * Handles the controlling of an AJAX Post Request when
     * a user submits the register form
     */
    private function post () {
        $ValidateModel = new ValidateModel();
        $ValidateModel->registerForm();
        if ($ValidateModel->result['success'] === false) {
            $this->returnResponse('AJAX', $ValidateModel->result);
        }
        // Hash the password and remove the raw password from everywhere
        $hash = password_hash($_REQUEST['password'], PASSWORD_BCRYPT);
        $_REQUEST['password'] = null;
        // Create the user in the database
        $data = [$_REQUEST['username'], $_REQUEST['email'], $hash, 1, 3];
        $DatabaseModel = new DatabaseModel();
        $DatabaseModel->runQuery(self::CREATE_USER, $data);
        if (!$DatabaseModel->row) {
            // this sholdnt happen but the database model has alreayd logged it
            $result = [
                'success' => false,
                'message' => 'An error occured whilst performing this action',
                'data' => 'register'
            ];
            $this->returnResponse('AJAX', $result);
        }
        $result = [
            'success' => true,
            'message' => 'Successfully created',
            'data' => 'register'
        ];
        $this->returnResponse('AJAX', $result);
    }

    /**
     * HTTP GET Request
     * 
     * Display the whole view
     */
    private function get () {
        $this->returnResponse('HTTP', $_SERVER['DOCUMENT_ROOT'] . $this->viewPath);
    }

 }
