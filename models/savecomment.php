<?php
	//Setting variables up
	$servername = "localhost";
	$username = "root";
	$password = "password";
	$author = $_POST['author'];
	$comment = $_POST['comment'];
	$date = $_POST['dateposted'];

	//create connection
	$connection = new mysqli($servername, $username, $password, 'copytube');

	//if connection works, set variable to string of inserting data
	$sql = "INSERT INTO comments (author, comment, dateposted) VALUES
	('".$author."', '".$comment."', '".$date."')";

	//set this data in the database
	$connection->query($sql);
	$connection->close();
?>