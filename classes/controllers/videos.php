
<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 25/02/2019
 * Time: 10:34
 */

$post = $_POST;

if($post['action'] === 'getVideos'){
    $videos = new Videos();
    print_r($videos->getAllVideos());
}