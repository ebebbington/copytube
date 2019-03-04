<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 10:34
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/videos.php';

//
// Set data
//
$postData = $_POST;
$videos = new Videos();
$possibleActions = ['getAllVideos'];

//
// Check if action has even been created for the request
//
if (!in_array($postData['action'], $possibleActions)) {
    print_r(json_encode(['Requested action does not exists']));
} else {
    print_r($videos->$postData['action']($postData));
}