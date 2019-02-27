<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 13:20
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/user.php';
//
// Set Data
//
    $action = $_POST['action'];
    $user = new User();
    $possibleActions = ['login', 'logout', 'register', 'recover'];

//
// Send to Login class
//
    if ($action === 'login') {
        print_r($user->login());
    }

//
// Send to Logout class
//
    if ($action === 'logout') {
        print_r($user->logout());
    }

//
// Send to Register class
//
    if ($action === 'register') {
        print_r($user->register());
    }

//
// Send to Recovery class
//
    if ($action === 'recover') {
        print_r($user->recover());
    }

//
// Check if action has even been created for request
//
    if (!in_array($action, $possibleActions)) {
        print_r(json_encode(['Requested action does not exist']));
    }