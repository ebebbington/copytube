<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:41
 */

// todo Add nice beautiful comments

include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/controllers/database.php';

class Videos
{
    private $videos;
    private $db;

    const GET_VIDEOS = "SELECT title, src, description, poster, width, height FROM videos";

    public function __construct()
    {
        $this->db = new Database();
    }

    //
    // Retrieve all Videos
    //
    public function getAllVideos()
    {
        try {
            $query = $this->db->connection->prepare(self::GET_VIDEOS);
            $query->execute();
            $this->videos = $query->get_result()->fetch_all(MYSQLI_ASSOC);
            return [true, 'Retrieved all videos', $this->videos];
        } catch (error $e) {
            new Mail('edward.bebbington@intercity.technology', 'Error: DB', $e);
            return [false, 'Database error when retrieving videos', null];
        } finally {
            $this->db->closeDatabaseConnection();
        }
        // Returned data is: title, src, description, poster, width, height
    }
}