<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:41
 */

include_once '../controllers/database.php';
$db = new Database();
$db = $db->connectToDatabase();

class Videos extends Database
{
    public function __construct() {
        $this->db = new Database;
    }

    public function getVideos () {
        $db = new Database;
        $db->connectToDatabase();
        $result = $db->connection->query($db::GET_VIDEOS);
        $db = new Database();
        $db->closeDatabaseConnection();
        $response = $result->fetch_all(MYSQLI_ASSOC);
        return json_encode($response);
    }
}