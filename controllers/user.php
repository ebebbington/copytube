<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 13:20
 */

require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/user.php';

//
// Set data
//
$data   = $_POST ?? $_GET;
$action = $data[ 'action' ];
$User   = new User();
//
// Quick null checks
//
if ( ! isset($data) || ! isset($action)) {
  print_r(json_encode(FALSE));
}

//
// Handle the different actions
//
switch ($data[ "action" ]) {
  case 'checkSession':
    $result = $user->checkSession();
    print json_encode($result);
    break;
  case 'login':
    $foundAccount = $User->findMatch($data['email'], $data['password']);
    switch ($foundAccount) {
      case false:
        print json_encode([
          'success' => false,
          'message' => 'Incorrect Email or Password',
          'data' => null
        ]);
        break;
      case true:
        $result = $User->login($data['email']);
        print json_encode($result);
        break;
    }
  case 'register':
    $Validate = new Validate();
    // Username
    $usernameResult = $Validate->validateUsername($data['username']);
    if ($usernameResult['success'] === false) {
      print json_encode($usernameResult);
      exit();
    }
    // Email
    $emailResult = $Validate->validateEmail($data['email']);
    if ($emailResult['success'] === false) {
      print json_encode($emailResult);
      exit();
    }
    // Password
    $passwordResult = $Validate->validatePassword($data['password']);
    if ($passwordResult['success'] === false) {
      print json_encode($passwordResult);
      exit();
    }
    // Compare username and password
    $username = $usernameResult['data'];
    $email = $emailResult['data'];
    $password = $passwordResult['data'];
    $isSimilar = $Validate->compareStrings($username, $password);
    if ($isSimilar) {
      print json_encode([
        'success' => false,
        'message' => 'Username and password cannot be the same or contain eachother',
        'data' => 'username'
      ]);
    }
    // Register account
    $result = $User->register($username, $email, $password);
    print json_encode($result);
    break;
  case 'logout':
    $logout = $user->logout();
    $db     = new Database();
    $db->closeDatabaseConnection();
    print_r(json_encode($logout));
    break;
  case 'recover':
    $user->recover($data);
    break;
  case 'getUser':
    $userArray = $user->getUser('');
    print_r($userArray);
    break;
  case 'getKey':
    $response = $user->getKey();
    print_r($response);
    break;
  default:
    // todo this should not happen
    break;
}