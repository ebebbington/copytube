<?php

    $servername = "localhost";
    $username = "root";
    $password = "password";
    $videotitle = $_GET['videotitle'];

    //create connection
    $connection = new mysqli($servername, $username, $password, 'copytube');

    //check connection
    if ($connection->connect_error) {
        die("connection to database inside getvideos.php has failed: " + $connection->connect_error);
    }

    //if connection works, set variable to string, get all from this and encode it
    $main_vid = "SELECT title, src, description, poster FROM videos WHERE title='$videotitle'";
    $rabbit_holes = "SELECT title, src, description, poster FROM videos WHERE title<>'$videotitle' ORDER BY id ASC";

    $result = $connection->query($main_vid);
    $result2 = $connection->query($rabbit_holes);

    $response = $result->fetch_all(MYSQLI_ASSOC);
    $response2 = $result2->fetch_all(MYSQLI_ASSOC);

    print_r(json_encode($response));
    print_r(json_encode($response2));

    $connection->close();

?>