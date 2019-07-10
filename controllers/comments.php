<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 11:25
 */

require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/comments.php';
require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/validate.php';

//
// Set data
//
$data     = $_POST ?? $_GET;
$action   = $data[ 'action' ];
$Comments = new Comments();

//
// Quick null checks
//
if ( ! isset($data) || ! isset($action)) {
  print_r(json_encode(FALSE));
}

//
// Handle the different actions
//
switch ($action) {
  case 'getComments':
    // Checks
    if ( ! $data[ 'videoTitle' ]) {
      print_r(json_encode(FALSE));
    }
    $comments = $Comments->getComments($data[ 'videoTitle' ]);
    print_r(json_encode($comments));
    break;
  case 'addComment':
    // Checks
    if ( ! $data[ 'author' ] || ! $data[ 'comment' ] || $data[ 'datePosted' ] || ! $data[ 'videoTitle' ]) {
      print_r(json_encode(FALSE));
    }
    $Validate = new Validate();
    $comment  = $Validate->validateComment($data);
    if ( ! $comment) {
      print_r(json_encode(FALSE));
    }
    $Comments->addComment($data);
    print_r(json_encode($comment));
    break;

  default:

    print_r(json_encode(FALSE));
}