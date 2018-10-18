<?php
	//Setting variables up
	$servername = "localhost";
	$username = "root";
	$password = "password";
	$database = "copytube";

	//create connection
	$connection = new mysqli($servername, $username, $password, $database);

	//check connection
	if ($connection->connect_error) {
		die("connection failed: " + $connection->connect_error);
	}

	//if connection works, set variable to string of inserting data
	$sql = "INSERT INTO comments (author, comment, dateposted) VALUES ('adam', 'you will get a bafta for this beauty', '10/10/2010')";

	//set this data in the database
	$connection->query($sql);
	$connection->close();
?>