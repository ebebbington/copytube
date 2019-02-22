<?php

$db = new Database();
$db->connectToDatabase();
//check connection
if ($db->connection->connect_error) {
    die("connection to database inside get_videos.php has failed: $connection->connect_error");
}
//if connection works, set variable to string, get all from this and encode it
$result = $db->connection->query($db::GET_VIDEOS);
$response = $result->fetch_all(MYSQLI_ASSOC);
print_r(json_encode($response));
$db->connection->close();