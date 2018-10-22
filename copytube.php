<!DOCTYPE html>

<!-- region html -->
<html> 
    <!-- region head -->
	<head>

        <!-- region To Do List -->
        <!-- TODO To Do List
        - display comments based on video
        - use top search bar to search for these videos
        - display correct name under rabbit hole vids
        - display correct name and description under main video when new video is clicked
        - I CAN HAVE THE TITLE OF RABBIT HOLE VIDEO AS AN ID UNDERNEATH EACH VIDEO AND ALSO GET 2 MORE RABBIT HOLE ID'S TO DISPLAY THE OTHER TWO
        - Get DB to display videos so i can remove code in .js file
        <!-- endregion-->

        <!-- region Tab Title & Welcome Message -->
		<title>CopyTube</title>
		<h2 id="welcome"></h2>
        <!-- endregion -->

        <!-- region Links & Scripts -->
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
        <!-- endregion -->

	</head>
    <!-- endregion -->

    <!-- region body -->
	<body>

        <!-- region Opening Page -->
		<div class="container">
			
			<!-- region Row for Logo, Search Bar and Search Button -->
			<div class="row">

				<!-- region Logo -->
				<div class="col-xs-3">

					<img id="logo" src="imageresources/CopyTube_Logo.png"/>

				</div>
                <!-- endregion -->

				<!-- region Search Bar/Button -->
				<div class="col-xs-9">
					<div id="search" class="input-group">
     					<input id="search-bar" type="text" class="form-control my-input" placeholder="Search..."/>
      					<span class="input-group-btn">
       						<button id="search-button" class="btn btn-default" type="button">Beep Boop Calculate!</button>
      					</span>
    				</div>
				</div>
                <!-- endregion -->

			</div>
            <!-- endregion -->

			<!-- region Row for Main Video/Title/Description, Comments Section & Rabbit Hole -->
			<div class="row">

				<!-- region Main Video/Title/Description & Comments Section-->
				<div class="col-xs-9">

                        <!-- region Main Video/Title/Description -->
						<br>
						<br>
						<br>
						<div class="my-video col-xs-12">
							<!-- video -->
							<video id='main-video' controls
									autoplay
							    	muted
							    	title="Big Buck Bunny Trailer (2018)"
                                    description="test bunny description .html code"
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
                        <!-- endregion -->

						<!-- region Comments Title/Bar & Button -->
						<p id="comment-title">Comments Section</p>
						<div id="comment" class="input-group">
							<!-- comments bar -->
							<input id="comment-bar" type="text" class="form-control my-input" placeholder="Add a comment..."/>
      						<span class="input-group-btn">
      							<!-- comments button -->
       							<button id="comment-button" class="btn btn-default" type="button">Add</button>
      						</span>
    					</div>
                        <!-- endregion -->
    					<br>
    					<!-- region User Comments -->
						<div class="user-comments">

								<!-- region Display New Comment & Comments from Database -->
								<div id="user-comments"></div>
								<br>
								<br>
								<div id="db-comments">

									<!-- region Get Database Comments -->
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
												echo "Username: " . $row["author"]. "<br>" . "Date: " . $row["dateposted"] . "<br>" . "Comment: " . $row["comment"]. "<br>" . "<br>" . "<br>";
											}
										}
										//close database connection
										$connection->close();
									?>
                                    <!-- endregion -->
								</div>
                                <!-- endregion -->
						</div>
                        <!-- endregion -->


				</div>
                <!-- endregion -->

                <!-- region Rabbit Hole -->
				<div class="col-xs-3">

					<div class="rabbit-holes col-xs-12">

							<video id='rabbit-hole-vid-1' class='rabbit-hole-vid' controls
							    	muted
							    	title="An Elephants Dream"
                                    description="test elephants dream description .html code"
							    	src="http://dl3.webmfiles.org/elephants-dream.webm"
							    	width="230"
							    	height="220">
							    Sorry, your browser doesn't support embedded videos.
							</video>
							<p id="rabbit-hole-vid-1-title"></p>
							
						</div>

						<div class="rabbit-hole col xs-12">

							<video id='rabbit-hole-vid-2' class='rabbit-hole-vid' controls
							    	muted
							    	title="Lego Display"
                                    description="test lego description .html code"
							    	src="http://techslides.com/demos/sample-videos/small.mp4"
							    	width="230"
							    	height="220">
							    Sorry, your browser doesn't support embedded videos.
							</video>
							<p id="rabbit-hole-vid-2-title"></p>
							
						</div>

					</div>

				</div>
                <!-- endregion -->

			</div>
            <!-- endregion -->

		</div>
        <!-- endregion -->

	</body>
    <!-- endregion -->

</html>
<!-- endregion -->