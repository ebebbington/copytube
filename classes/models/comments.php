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
    private $user;

    //
    // SQL Strings
    //
    //const GET_COMMENTS = "SELECT title, author, comment, dateposted FROM comments WHERE title = ? ORDER BY ASC";
    const GET_COMMENTS = "SELECT title, author, comment, dateposted FROM comments ORDER BY dateposted ASC";
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
        $comments = $query->get_result()->fetch_all(MYSQLI_ASSOC);
        $this->db->closeDatabaseConnection();
        print_r(json_encode($comments));
    }

    //
    // Add a Comment to DB
    //
    public function addComment ($postData) {
        $comment = $postData[0];
        $datePosted = $postData[1];
        $videoTitle = $postData[2];
        $user = new User();
        $author = $user->getUser('return');
        $query = $this->db->connection->prepare(self::ADD_COMMENT);
        $query->bind_param('ssss', $author, $comment, $datePosted, $videoTitle);
        $query->execute();
        if ($query->affected_rows === 1) {
            print_r(json_encode([$author, $comment, $datePosted, $videoTitle]));
        } else {
            print_r(json_encode(false));
        }
        $this->db->closeDatabaseConnection();
    }
}