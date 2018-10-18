<?php
	//Setting variables up
	$servername = "localhost";
	$username = "root";
	$password = "password";

	//create connection
	$connection = new mysqli($servername, $username, $password, 'copytube');

	//if connection works, set variable to string of inserting data
	//$sql = "INSERT INTO comments (author, comment, dateposted) VALUES ($_POST['author'], $_POST['comment'], $_POST['dateposted'])";
	$sql = "INSERT INTO comments (author, comment, dateposted) VALUES ('ajaxusername', 'testcomment', '1010/10/10')";

	//set this data in the database
	$connection->query($sql);
	$connection->close();
?>