<?php
				// $username = 
				// $description = 
				// $date = 
				// $time =

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

				$sql = "INSERT INTO comments (comment, author, `date`, `time`) VALUES ('testcomment', 'testauthor', 'testdate', 'testtime')";

				$connection->query($sql);
				//die($connection->error);
				$connection->close();
?>