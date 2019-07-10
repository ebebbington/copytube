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
    $isLoggedIn = $user->checkSession();
    $db         = new Database();
    $db->closeDatabaseConnection();
    print_r(json_encode($isLoggedIn));
    break;
  case 'login':
    $login = $user->login($data);
    $db    = new Database();
    $db->closeDatabaseConnection();
    print_r(json_encode($login));
    break;
  case 'register':
    $usernamePass = $User->register($data);
    if ($usernamePass[1] === false)  {
      print_r(json_encode([$usernamePass[0], $user]))
    }
    $db         = new Database();
    $db->closeDatabaseConnection();
    print_r(json_encode($registered));
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