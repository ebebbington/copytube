<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:41
 */

include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/controllers/database.php';

class Videos
{
    const GET_VIDEO_BY_TITLE = "SELECT * FROM videos WHERE title = ?";
    const GET_VIDEOS_FOR_RABBIT_HOLE = "SELECT * FROM videos WHERE title != ? LIMIT 2" // not main video title

    public function __construct()
    {
    }

    /**
     * Get a single video matching the clicked one
     * 
     * @param String $videoTitle The clicked video's title
     * @return Array [rowCount, data, success, message]
     */
    public function getClickedVideo (String $videoTitle = '') {
        $db = new Database();
        $result = $db->runQuery(self::GET_VIDEO_BY_TITLE, [$videoTitle]);
        return $result;
    }

    /**
     * Get rabbit hole videos that arent the clicked video
     * 
     * This section is executed when a main video is found i.e. find the main video then get the rabbit hole videos
     * 
     * @param String $mainVideoTitle The main video's title to NOT grab
     * @return Array [rowCount, data, success, message]
     */
    public function getVideosForRabbitHole (String $mainVideoTitle = '') {
        $db = new Database();
        $result = $db->query->runQuery(self::GET_VIDEOS_FOR_RABBIT_HOLE, [$mainVideoTitle]);
        return $result;
    }
}