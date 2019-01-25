<?php
/**
 * Created by PhpStorm.
 * User: bebbe
 * Date: 23/10/2018
 * Time: 13:02
 */
$serverName = "localhost";
$username = "root";
$password = "password";
$videoTitle = $_GET['videoTitle'];
//create connection
$connection = new mysqli($serverName, $username, $password, 'copytube');
//check connection
if ($connection->connect_error) {
    die("connection to database inside getcomment.php has failed: " + $connection->connect_error);
}
//if connection works, set variable to string, get all from this and encode it
$sql = "SELECT author, comment, dateposted FROM comments WHERE title='$videoTitle'";
$result = $connection->query($sql);
$response = $result->fetch_all(MYSQLI_ASSOC);
print_r(json_encode($response));
$connection->close();