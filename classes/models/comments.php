<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:56
 */

include_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/controllers/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/user.php';

class Comments
{
    //
    // Public Variables
    //
    public $commentData;
    private $db;

    //
    // SQL Strings
    //
    //const GET_COMMENTS = "SELECT title, author, comment, dateposted FROM comments WHERE title = ? ORDER BY ASC";
    const GET_COMMENTS = "SELECT title, author, comment, dateposted FROM comments ORDER BY ASC";
    const ADD_COMMENT = "INSERT INTO comments (author, comment, dateposted, title) VALUES (?, ?, ?, ?)";

    public function __construct() {
        $this->db = new Database();
        $this->db->openDatabaseConnection();
    }

    //
    // Retrieve Comments from DB
    //
    public function getComments () {
        $query = $this->db->connection->prepare(self::GET_COMMENTS);
        $query->execute();
        $comments = [[]];
        for ($i = 0, $l = $query->affected_rows; $i < $l; $i++) {
            $query->bind_result($comments[$i]['title'], $comments[$i]['author'], $comments[$i]['comment'],
              $comments[$i]['description']);
            $query->fetch();
        }
        $this->db->closeDatabaseConnection();
        print_r(json_encode($comments));
    }

    //
    // Add a Comment to DB
    //
    public function addComment ($postData) {
        // todo :: create validation
        $user = new User();
        $author = $user->username; // todo :: username
        $comment = $postData['comment'];
        $datePosted = $postData['datePosted'];
        $title = $postData['videoTitle'];
        $query = $this->db->connection->prepare(self::ADD_COMMENT);
        $query->bind_param('ssss', $author, $comment, $datePosted, $title);
        $query->execute();
        $this->db->closeDatabaseConnection();
        if ($query->affected_rows > 1 || $query->affected_rows < 1) {
            print_r(json_encode($author));
        } else {
            return json_encode(false);
        }
    }
}