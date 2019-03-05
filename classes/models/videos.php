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
        $query = $this->db->connection->prepare(self::GET_VIDEOS);
        $query->execute();
        $videos = [[]];
        for ($i = 0, $l = $query->affected_rows; $i < $l; $i++) {
            $query->bind_result($videos[$i]['title'], $videos[$i]['src'], $videos[$i]['description'],
              $videos[$i]['poster'], $videos[$i]['width'], $videos[$i]['height']);
            $query->fetch();
        }
        $this->db->closeDatabaseConnection();
        print_r(json_encode($videos));
    }
}