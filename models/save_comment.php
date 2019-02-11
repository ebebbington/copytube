<?php
//Setting variables up
$servername = "localhost";
$username = "root";
$password = "password";
$author = $_POST['author'];
$comment = $_POST['comment'];
$date = $_POST['datePosted'];
$videoTitle = $_POST['videoTitle'];
//create connection
$connection = new mysqli($servername, $username, $password, 'copytube');
//check connection
if ($connection->connect_error) {
    die("connection to database inside savecomment.php has failed: " + $connection->connect_error);
}
//if connection works, set variable to string of inserting data
$sql = "INSERT INTO comments (author, comment, dateposted, title) VALUES
('" . $author . "', '" . $comment . "', '" . $date . "', '" . $videoTitle . "')";
//set this data in the database
$connection->query($sql);
$connection->close();