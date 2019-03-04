<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:41
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/controllers/database.php';

class Videos
{
    const GET_VIDEOS = "SELECT title, src, description, poster, width, height FROM videos";

    public function __construct() {
        $this->db = new Database();
        $this->db->openDatabaseConnection();
    }

    //
    // Retrieve all Videos
    //
    public function getAllVideos () {
        $result = $this->db->connection->query(self::GET_VIDEOS);
        $response = $result->fetch_all(MYSQLI_ASSOC);
        $this->db->closeDatabaseConnection();
        return $response;
    }
}