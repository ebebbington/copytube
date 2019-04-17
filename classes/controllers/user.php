<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 13:20
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/user.php';

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
$user = new User();
$possibleActions = ['login', 'logout', 'register', 'recover', 'getUser', 'checkSession', 'getKey'];

if (!in_array($action, $possibleActions)) {
    exit();
} else {
    if ($action === 'login') {
        $response = $user->login($data);
        print_r($response);
        $db = new Database();
        $db->closeDatabaseConnection();
    }

    if ($action === 'register') {
        $user->register($data);
        $db = new Database();
        $db->closeDatabaseConnection();
    }

    if ($action === 'logout') {
        $response = $user->logout();
        $response = json_encode($response);
        print_r($response);
        $db = new Database();
        $db->closeDatabaseConnection();
    }

    if ($action === 'recover') {
        $user->recover($data);
    }

    if ($action === 'getUser') {
        $userArray = $user->getUser('');
        print_r($userArray);
    }

    if ($action === 'checkSession') {
        $isLoggedIn = $user->checkSession();
        $isLoggedIn = json_encode($isLoggedIn);
        print_r($isLoggedIn);
    }

    if ($action === 'getKey') {
        $response = $user->getKey();
        print_r($response);
    }
}