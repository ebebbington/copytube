<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 13:20
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/user.php';

//
// todo :: possibly filter out GETs and POSTs
//
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = $_POST;
    $action = $_POST['action'];
}
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = $_GET;
    try {
        $action = $_POST['action'];
    } catch (Exception $e) {
        $action = $_POST['action'];
    }
}
if (!isset($data)) {
    print_r(json_encode(['No data has been sent']));
}
//
// Set Data
//
$postAction = $_POST['action'];
$user = new User();
$possibleActions = ['login', 'logout', 'register', 'recover', 'getUser', 'checkSession', 'getKey'];

if (!in_array($postAction, $possibleActions)) {
    exit();
} else {
    if ($action === 'login') {
        $user->login($data);
    }

    if ($action === 'register') {
        $user->register($data);
    }

    if ($action === 'logout') {
        $user->logout();
    }

    if ($action === 'recover') {
        $user->recover($data);
    }

    if ($action === 'getUser') {
        $user->getUser('');
    }

    if ($action === 'checkSession') {
        $user->checkSession();
    }

    if ($action === 'getKey') {
        $response = $user->getKey();
        print_r($response);
    }
}