<?php
$serverName = "localhost";
$username = "root";
$password = "password";
//create connection
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection
if ($connection->connect_error) {
    die("connection to database inside get_videos.php has failed: $connection->connect_error");
}
//if connection works, set variable to string, get all from this and encode it
$sql = "SELECT title, src, description, poster, width, height FROM videos";
$result = $connection->query($sql);
$response = $result->fetch_all(MYSQLI_ASSOC);
print_r(json_encode($response));
$connection->close();