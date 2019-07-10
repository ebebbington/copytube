<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:41
 */

// todo Add nice beautiful comments

include_once $_SERVER['DOCUMENT_ROOT'] . '/controllers/database.php';

class Videos
{
    const GET_VIDEOS = "SELECT title, src, description, poster, width, height FROM videos";

    private $videos;
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    //
    // Retrieve all Videos
    //
    public function getAllVideos () {
        try {
            $query = $this->db->connection->prepare(self::GET_VIDEOS);
            $query->execute();
            $this->videos = $query->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (error $e) {
            // todo gather details of error and log/email
            $this->videos = false;
        } finally {
            $this->db->closeDatabaseConnection();
            return $this->videos;
        }
        // Returned data is: title, src, description, poster, width, height
    }
}