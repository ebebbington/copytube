<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 13:20
 */


require_once '../models/user.php';
//
// Set Data
//
    $action = $_POST['action'];
    $user = new User();
    $possibleActions = ['login', 'logout', 'register', 'recover'];

    if (!in_array($action, $possibleActions)) {
        print_r(json_encode(['Requested action does not exist']));
    } else {
        // Run specified action/function i.e. send to the function it needs
        print_r($user->$action());
    }