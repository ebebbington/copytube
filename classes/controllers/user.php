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
// todo :: Problem with this controller is AJAX requests need to pass in an action and run script, but when i include this script i don't want it to run but i need to call a function within
    $action = $_POST['action'];
    $user = new User();
    $possibleActions = ['login', 'logout', 'register', 'recover'];

    if (!in_array($action, $possibleActions)) {
        print_r(json_encode(['Requested action does not exist']));
    } else {
        // Run specified action/function i.e. send to the function it needs
        print_r($user->$action());
    }