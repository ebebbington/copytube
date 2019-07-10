<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 10:34
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/models/videos.php';

//
// Set data
//
$postData = $_POST;
$postAction = $_POST['action'];
$videos = new Videos();
$possibleActions = ['getAllVideos'];

//
// Check if action has even been created for the request
//
if (!in_array($postAction, $possibleActions)) {
    exit();
} else {

    if ($postAction === 'getAllVideos') {
        $videos->getAllVideos();
    }
}