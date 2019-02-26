<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:56
 */

include_once '../controllers/database.php';

class Comments
{
    //
    // Public Variables
    //
    public $commentData;

    //
    // SQL Strings
    //
    const GET_COMMENTS = "SELECT title, author, comment, dateposted FROM comments WHERE title = ? ORDER BY ASC";
    const ADD_COMMENT = "INSERT INTO comments (comment, author, dateposted, title) VALUES (?, ?, ?, ?)";

    //
    // Retrieve Comments from DB
    //
    public function getComments ($videoTitle) {
        $db = new Database();
        $db->openDatabaseConnection();
        $query = $db->connection->prepare(self::GET_COMMENTS);
        $query->execute($videoTitle);
        $comments = $query->fetch_all(MYSQLI_ASSOC);
        $db->closeDatabaseConnection();
        return json_encode($comments);
    }

    //
    // Add a Comment to DB
    //
    public function addComment ($commentData) {
        $db = new Database();
        $db->openDatabaseConnection();
        $query = $db->connection->prepare(self::ADD_COMMENT);
        $query->execute($commentData);
        $affectedRows = $query->rowCount();
        if ($affectedRows < 1 || $affectedRows > 1) {
            $db->closeDatabaseConnection();
            return json_encode(['Query did not affect the database']);
        } else {
            $db->closeDatabaseConnection();
            return json_encode([true]);
        }
    }
}