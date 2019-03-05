<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 11:25
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/comments.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/validate.php';

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
    exit();
} else {

    if ($postAction === 'getComments') {
        $comments->getComments();
    }

    if ($postAction === 'addComment') {
        $validate = new Validate();
        $validate->validateComment($postData);
    }
}