<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 13:20
 */

//
// Set Data
//
$post = $_POST;
$user = new User();
$possibleActions = ['login', 'logout', 'register', 'recover'];

//
// Send to Login class
//
if ($post['action'] === 'login') {
    print_r($user->login());
}

//
// Send to Logout class
//
if ($post['action'] === 'logout') {
    print_r($user->logout());
}

//
// Send to Register class
//
if ($post['action'] === 'register') {
    print_r($user->register());
}

//
// Send to Recovery class
//
if ($post['action'] === 'recover') {
    print_r($user->recover());
}

//
// Check if action has even been created for request
//
if (!in_array($post['action'], $possibleActions)) {
    print_r(json_encode(['Requested action does not exist']));
}