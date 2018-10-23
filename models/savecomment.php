<?php
	//Setting variables up
	$servername = "localhost";
	$username = "root";
	$password = "password";
	$author = $_POST['author'];
	$comment = $_POST['comment'];
	$date = $_POST['dateposted'];
	$videotitle = $_POST['videotitle'];

	//create connection
	$connection = new mysqli($servername, $username, $password, 'copytube');

	//if connection works, set variable to string of inserting data
	$sql = "INSERT INTO comments (author, comment, dateposted, video) VALUES
	('".$author."', '".$comment."', '".$date."', '".$videotitle."')";

	//set this data in the database
	$connection->query($sql);
	$connection->close();
?>