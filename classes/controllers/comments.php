<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 11:25
 */

//
// Set data
//
$post = $_POST;
$comments = new Comments();
$possibleActions = ['getComments', 'addComment'];

//
// Retrieve Comments
//
if ($post['action'] === 'getComments') {
    print_r($comments->getComments($_POST['videoTitle']));
}

//
// Add a comment
//
if ($post['action'] === 'addComment') {
    try{
        $commentData = [ $_POST['comment'], $_POST['author'], $_POST['datePosted'], $_POST['videoTitle'] ];
        $comments->addComment($commentData);
    } catch (exception $error) {
        print_r(json_encode(['Please provide a comment']));
    }
}

//
// Check if action has even been created for request
//
if (!in_array($post['action'], $possibleActions)) {
    print_r(json_encode(['Requested action does not exist']));
}