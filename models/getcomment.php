<?php
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

    //if connection works, set variable to string
    $sql = "SELECT * FROM comments (author, comment, dateposted) WHERE video=$videotitle";

    //set this data in the database DO I NEED THIS:--------------------
    //$connection->query($sql);
    //$connection->close();
    //-----------------------------------------------------------------