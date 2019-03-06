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
$postData = $_POST;
$postAction = $_POST['action'];
$user = new User();
$possibleActions = ['login', 'logout', 'register', 'recover', 'getUser'];

if (!in_array($postAction, $possibleActions)) {
    exit();
} else {

    if ($postAction === 'login') {
        $user->login($postData);
    }

    if ($postAction === 'register') {
        $user->register($postData);
    }

    if ($postAction === 'logout') {
        $user->logout();
    }

    if ($postAction === 'recover') {
        $user->recover($postData);
    }

    if ($postAction === 'getUser') {
        $user->getUser();
    }
}