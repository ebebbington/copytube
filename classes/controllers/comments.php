<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 11:25
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/comments.php';

//
// Set data
//
$postData = $_POST;
$postAction = $_POST['action'];
$comments = new Comments();
$possibleActions = ['getComments', 'addComment'];

//
// Check if action has even been created for request
//
if (!in_array($postAction, $possibleActions)) {
    print_r(json_encode(['Requested action does not exist']));
} else {
    $comments->$postAction($postData);
}