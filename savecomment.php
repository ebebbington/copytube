<?php

	// $username = 
	// $description = 
	// $date = 
	// $time =

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
	die(var_dump($_POST));
	//$sql = "INSERT INTO comments (comment, author) VALUES ($_POST['author'], $_POST['comment'])";

	//set this data in the database
	$connection->query($sql);
	die($connection->error);
	$connection->close();
?>