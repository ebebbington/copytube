<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:41
 */

include_once '../controllers/database.php';

class Videos
{
    const GET_VIDEOS = "SELECT title, src, description, poster, width, height FROM videos";

    //
    // Retrieve all Videos
    //
    public function getAllVideos () {
        $db = new Database();
        $db->openDatabaseConnection();
        $result = $db->connection->query(self::GET_VIDEOS);
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $db->closeDatabaseConnection();
        return json_encode($response);
    }
}