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

    public function getAllVideos () {
        $db = new Database();
        $db->connectToDatabase();

        $result = $db->connection->query(GET_VIDEOS);
        $response = $result->fetch_all(MYSQLI_ASSOC);

        $db->closeDatabaseConnection();
        return json_encode($response);
    }
}