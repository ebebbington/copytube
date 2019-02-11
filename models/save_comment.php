<?php
//Setting variables up
$servername = "localhost";
$username = "root";
$password = "password";
$author = $_POST['']; // todo :: how to pass in cookie
$comment = $_POST['comment-bar'];
$date = $_POST['']; // todo :: create date
$videoTitle = $_POST['videoTitle']; // todo :: how to get title
// todo :: add in serverside validation using link: https://www.youtube.com/watch?time_continue=223&v=sgrvuMlf93w
if (isset($_POST['submit'])) {
    if (empty($author)) {
        $error = "<p>Please enter name";
    } else {
        if (!preg_match('/'))
    }
}
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