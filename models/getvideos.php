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
    $sql = "SELECT title, src, description, poster, width, height FROM videos";

    $result = $connection->query($sql);

    $response = $result->fetch_all(MYSQLI_ASSOC);

    print_r(json_encode($response));

    $connection->close();

?>