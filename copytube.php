<!DOCTYPE html>

<html> 

	<head>

		<!--<To do>
			- Sort out click event of changing video - make only clicked video change and not all 3
			- I COULD, instead of finding name of clicked video, find it on the main video after it has changed and assign this to a variable
			- Create a local database
				- Create array on document load
					- store comments into this array
						- display comments based on video
							- use top search bar to search for these videos
			- display correct name under rabbit hole vids
			- display correct name and description under main video when new video is clicked
			- I CAN HAVE THE TITLE OF RABBIT HOLE VIDEO AS AN ID UNDERNEATH EACH VIDEO AND ALSO GET 2 MORE RABBIT HOLE ID'S TO DISPLAY THE OTHER TWO
			Save data to the database and try JSON (saving objects)
			- Get DB to display videos so i can remove code in .js file
		 <To do/>-->

		 <!-- insert title of tab and welcome message -->
		<title>CopyTube</title>
		<h2 id="welcome"></h2>

		<!-- allows .js code to run jquery -->
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

		<!-- accesses bootstrap css files that makes css files much easier to use -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<!-- accesses bootstrap js files that makes js files much easier to use -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		<!-- links my style sheet (.css) so it can be used -->
		<link rel="stylesheet" href="copytube.css"/>

		<!-- links my javascript sheet so it can be used -->
		<script src="copytube.js"></script>

	</head>

	<body>

		<div class="container">
			
			<!-- row to include logo and search bar and button -->
			<div class="row">

				<!-- logo -->
				<div class="col-xs-3">

					<img id="logo" src="imageresources/CopyTube_Logo.png"/>

				</div>

				<!-- search bar/button -->
				<div class="col-xs-9">
					<div id="search" class="input-group">
     					<input id="search-bar" type="text" class="form-control my-input" placeholder="Search..."/>
      					<span class="input-group-btn">
       						<button id="search-button" class="btn btn-default" type="button">Beep Boop Calculate!</button>
      					</span>
    				</div>

				</div>

			</div>

			<!-- new row to insert main video, title & description, and comments section and rabbit hole videos -->
			<div class="row">
				<!-- insert main video, title, description and comments section and rabbit hole videos-->
				<div class="col-xs-9">

						<br>
						<br>
						<br>
						<!-- contains video, title and description -->
						<div class="my-video col-xs-12">
							<!-- video -->
							<video id='main-video' controls
									autoplay
							    	muted
							    	src="http://dl3.webmfiles.org/big-buck-bunny_trailer.webm"
							    	width="750"
							    	height="400">
							    Sorry, your browser doesn't support embedded videos.
							</video>
							<!-- main video title -->
							<p id="main-video-title"></p> 
							<br>
							<!-- main video description -->
							<p id="main-video-description"></p> 
						<br>
						<br>
						</div>
						<!-- comments title -->
						<p id="comment-title">Comments Section</p>
						<div id="comment" class="input-group">
							<!-- comments bar -->
							<input id="comment-bar" type="text" class="form-control my-input" placeholder="Add a comment..."/>
      						<span class="input-group-btn">
      							<!-- comments button -->
       							<button id="comment-button" class="btn btn-default" type="button">Add</button>
      						</span>
    					</div>
    					<br>
    					<!-- user comments -->
						<div class="user-comments">
								<!-- display new comment -->
								<div id="user-comments"></div>
								

								<!-- display comments from database -->
								<div id="db-comments">
									<!-- start of php database grabbing -->
									<?php
										// setting variables
										$servername = "localhost";
										$username = "root";
										$password = "password";
										//create connection
										$connection = new mysqli($servername, $username, $password, 'copytube');
										//check connection
										if ($connection->connect_error) {
											die("connection failed: " + $connection->connect_error);
										}
										// variables to equal all rows of comments and result of this
										$sql = "SELECT * FROM `comments`";
										$result = $connection->query($sql);
										// fetch all comments from table
										if ($result->num_rows > 0) {
											while($row = $result->fetch_assoc()) {
												echo "Author: " . $row["author"]. "<br>" . "Date: " . $row["date"] . "<br>" . "Comment: " . $row["comment"]. "<br>" . "<br>" . "<br>";
											}
										}
										//close database
										$connection->close();
									?>
								</div>

						</div>


				</div>

				<!-- rabbit hole videos -->
				<div class="col-xs-3">

					<div class="row" id="rabbit-holes"></div>

				</div>
				
			</div>

		</div>

	</body>

</html>