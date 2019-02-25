<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 10:34
 */


//
// Set data
//
$post = $_POST;
$videos = new Videos();
$possibleActions = ['getVideos'];

//
// Retrieve Videos
//
if($post['action'] === 'getVideos'){
    print_r($videos->getAllVideos());
}

//
// Check if action has even been created for the request
//
if (!in_array($post['action'], $possibleActions)) {
    print_r(json_encode(['Requested action does not exists']));
}