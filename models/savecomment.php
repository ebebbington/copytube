<?php
	//Setting variables up
	$servername = "localhost";
	$username = "root";
	$password = "password";

	//create connection
	$connection = new mysqli($servername, $username, $password, 'copytube');
	var_dump($_POST);
	$date = $_POST['dateposted'];
	//if connection works, set variable to string of inserting data
	//$sql = "INSERT INTO comments (author, comment, dateposted) VALUES ($_POST['author'], $_POST['comment'], $_POST['dateposted'])";
	$sql = "INSERT INTO comments (author, comment, dateposted) VALUES 
	('abc', 'def', '".$date."')";
	var_dump($sql);

	//set this data in the database
	$connection->query($sql);
	$connection->close();
?>