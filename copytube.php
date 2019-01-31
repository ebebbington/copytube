<!DOCTYPE html>

<html>

    <!-- Set data before DOM -->
	<head>
		<title>CopyTube</title>
		<h2 id="welcome-message"></h2>
		<!-- allows .js code to run jquery -->
		<script src="scripts/jquery-3.3.1.min.js"></script>
		<!-- accesses bootstrap css files that makes css files much easier to use -->
		<link rel="stylesheet" href="links/bootstrap.min.css" crossorigin="anonymous">
		<!-- accesses bootstrap js files that makes js files much easier to use -->
		<script src="scripts/bootstrap.min.js" crossorigin="anonymous"></script>
		<!-- NOTE: My files are placed after so they overite the files above if needed i.e. my css > their css styles -->
        <!-- links my style sheet (.css) so it can be used -->
		<link rel="stylesheet" href="copytube.css"/>
		<!-- links my javascript sheet so it can be used -->
		<script src="copytube.js"></script>
	</head>

	<body>
		<div class="container">
			<div class="row">
                <!-- set logo -->
				<div class="col-xs-3">
                    <!-- todo :: rename imageresources folder and copytube logo file, things to do are below:
                    rename code below, rename poster for main vid in .php, rename in DB -->
					<img id="logo" src="imageresources/CopyTube_Logo.png"/>
				</div>
				<!-- create search bar and button -->
				<div class="col-xs-9">
					<div id="search" class="input-group">
                        <div class="dropdown">
     					    <input id="search-bar" type="text" class="form-control my-input" placeholder="Hover Over Me for a List of Videos"/>
                            <div class="dropdown-content">
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
                                    $sql = "SELECT title FROM `videos`";
                                    $result = $connection->query($sql);
                                    // fetch all videos from table
                                    $count = 1;
                                    if ($result->num_rows > 0) {
                                        $html = "";
                                        while($row = $result->fetch_assoc()) {
                                            $html = "<a href='#'>$row[title]</a>";
                                            echo $html;
                                        }
                                    }
                                ?>
                            </div>
                        </div>
      					<span class="input-group-btn">
       						<button id="search-button" class="btn btn-default" type="button">Search</button>
      					</span>
    				</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-9">
                        <!-- display main video, title and description -->
						<br>
						<br>
						<br>
						<div class="my-video col-xs-12">
							<video id='main-video' controls
									autoplay
							    	muted
                                    poster="imageresources/something_more.jpg"
							    	title="Something More"
							    	src="videos/something-more.mp4"
							    	width="750"
							    	height="400">
							    Sorry, your browser doesn't support embedded videos.
							</video>
							<p id="main-video-title">Something More</p>
							<br>
							<p id="main-video-description">Watch this inspirational video as we look at all of the beautiful things inside this world</p>
						<br>
						<br>
						</div>

						<!-- create comments section -->
                        <p id="comment-title">Comments Section</p> <p id="comment-count">0</p>
						<div id="comment" class="input-group">
							<input id="comment-bar" type="text" class="form-control my-input" placeholder="Add a comment..."/>
      						<span class="input-group-btn">
       							<button id="comment-button" class="btn btn-default" type="button">Add</button>
      						</span>
    					</div>
    					<br>
                        <div id="user-comments"></div>
                        <div id="db-comments">
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
                                $sql = "SELECT * FROM `comments` WHERE title='Something More' ORDER BY `dateposted` DESC";
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
                        </div>
				</div>

                <!-- set rabbit hole -->
				<div class="col-xs-3">
					<div class="rabbit-holes col-xs-12">
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
                            $sql = "SELECT poster, title, src, width, height FROM `videos` WHERE title<>'Something More'";
                            $result = $connection->query($sql);
                            // fetch all videos from table
                            $count = 1;
                            if ($result->num_rows > 0) {
                                $html = "";
                                while($row = $result->fetch_assoc()) {
                                    $html = "<video id='rabbit-hole-vid-$count' class='rabbit-hole-vids' controls ".
                                        "muted ".
                                        "poster='$row[poster]' ".
                                        "title='$row[title]' ".
                                        "src='$row[src]' ".
                                        "width='$row[width]' ".
                                        "height='$row[height]'>".
                                        "</video>";
                                    echo $html;
                                    //create and display titles
                                    echo "<p id='rabbit-hole-vid-$count-title' class='rabbit-hole-titles'>$row[title]</p>";
                                    $count++;
                                }
                            }
                            //close database connection
                            $connection->close();
                        ?>
                    </div>
				</div>
			</div>
		</div>
	</body>
</html>