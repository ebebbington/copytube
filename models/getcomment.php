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
    $videotitle = $_GET['videotitle'];

    //create connection
    $connection = new mysqli($servername, $username, $password, 'copytube');

    //check connection
    if ($connection->connect_error) {
        die("connection to database inside getcomment.php has failed: " + $connection->connect_error);
    }

    //if connection works, set variable to string
    $sql = "SELECT author, comment, dateposted FROM comments WHERE video='Lava Sample'";
    $result = $connection->query($sql);
    $response = $result->fetch_all(MYSQLI_ASSOC);
    print_r(json_encode($response));
    $connection->close();
?>