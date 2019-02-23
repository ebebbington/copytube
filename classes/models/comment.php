<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:56
 */

class Comment extends Database
{
    const GET_COMMENTS = "SELECT * FROM comments";

    public function __construct() {
        $this->connectToDatabase = $this->openDatabaseConnection();


    }

    public function getComments () {
        $result = $databaseConnection->connection->query(self::GET_COMMENTS);
        $comments = $result->fetch_all(MYSQLI_ASSOC);
        $databaseConnection->closeDatabaseConnection();
        return $comments;
    }

    public function addComment ($username, $comment, $date, $videoTitle) {
        $databaseConnection = new Database();
        $databaseConnection->openDatabaseConnection();
    }
}