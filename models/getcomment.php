[<?php
    /**
     * Created by PhpStorm.
     * User: bebbe
     * Date: 23/10/2018
     * Time: 13:02
     */
    $servername = "localhost";
    $username = "root";
    $password = "password";
    $author = $_GET['author'];
    $comment = $_GET['comment'];
    $date = $_GET['dateposted'];
    $videotitle = $_GET['videotitle'];

    //create connection
    $connection = new mysqli($servername, $username, $password, 'copytube');

    //check connection
    if ($connection->connect_error) {
        die("connection to database inside getcomment.php has failed: " + $connection->connect_error);
    }
    die("connection to database inside getcomment.php has completed");

    //if connection works, set variable to string
    $sql = "SELECT (author, comment, dateposted) FROM `comments` WHERE video='Lava Display'";