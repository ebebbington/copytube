<?php
/**
 * Created by PhpStorm.
 * User: Edward
 * Date: 22/02/2019
 * Time: 23:56
 */

include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/controllers/database.php';
require_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/user.php';
include_once $_SERVER[ 'DOCUMENT_ROOT' ] . '/models/validate';

class Comments
{
    //
    // Public Variables
    //
    public $commentData;
    private $comments;
    private $db;
    private $user;
    private $result;

    //
    // SQL Strings
    //
    const GET_COMMENTS = "SELECT title, author, comment, dateposted FROM comments WHERE title = ? ORDER BY dateposted ASC";

    const ADD_COMMENT = "INSERT INTO comments (author, comment, dateposted, title) VALUES (?, ?, ?, ?)";

    public function __construct()
    {
        $this->db = new Database();
        $this->db->openDatabaseConnection();
    }

    //
    // Retrieve Comments from DB
    //
    public function getComments($videoTitle)
    {
        try {
            $query = $this->db->connection->prepare(self::GET_COMMENTS);
            $query->bind_param('s', $videoTitle);
            $query->execute();
            $this->comments = $query->get_result()->fetch_all(MYSQLI_ASSOC);

            return [true, 'Successfully retrieved comments', $this->comments];
        } catch (error $e) {
            new Mail(
              'edward.bebbington@intercity.technology',
              'Error: getComments',
              $e
            );

            return [false, 'There was an error retrieving comments', $e];
        } finally {
            $this->db->closeDatabaseConnection();
        }
    }

    //
    // Add a Comment to DB
    //
    public function addComment($data)
    {

        $comment    = $data[ 'comment' ];
        $datePosted = $data[ 'datePosted' ];
        $videoTitle = $data[ 'videoTitle' ];
        $User       = new User();
        $this->user = $User->getUser();
        $author     = $this->user[ 0 ][ 'author' ];
        if ( ! $comment || ! $datePosted) {
            return false;
        }
        if ($author) {
            try {
                $query = $this->db->connection->prepare(self::ADD_COMMENT);
                $query->bind_param('ssss', $author, $comment, $datePosted, $videoTitle);
                $query->execute();
                if ($query->affected_rows !== 1) {
                    return false;
                }

                // todo don't add a comment using JS, i should make ths seamless, possibly call the getComments function in JS at success of ajax call?
                return [$author, $comment, $datePosted, $videoTitle];
            } catch (error $e) {
                // todo log or email this error
            }
        }
    }
}