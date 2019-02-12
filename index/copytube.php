<!DOCTYPE html>

<html>
    <!-- Set data before DOM -->
	<head>
		<title>CopyTube</title>
		<!-- allows .js code to run jquery -->
		<script src="../scripts/jquery-3.3.1.min.js"></script>
		<!-- accesses bootstrap css files that makes css files much easier to use -->
		<link rel="stylesheet" href="../links/bootstrap.min.css" crossorigin="anonymous">
		<!-- accesses bootstrap js files that makes js files much easier to use -->
		<script src="../scripts/bootstrap.min.js" crossorigin="anonymous"></script>
		<!-- NOTE: My files are placed after so they overwrite the files above if needed i.e. my css > their css styles -->
        <!-- links my style sheet (.css) so it can be used -->
		<link rel="stylesheet" href="copytube.css"/>
        <!-- Link my learning script to prepare the data -->
        <script src="../learning.js"></script>
        <!-- Link my javascript file so it can be used -->
        <script src="copytube.js"></script>
	</head>

	<body>
    <h2 id="welcome"><script>getUsername('welcome message')</script></h2>
		<div class="container">
			<div class="row">
                <!-- set logo -->
				<div class="col-xs-3">
					<img id="logo" src="../images/copytube_logo.png" alt="Error locating image"/>
				</div>
				<!-- create search bar and button -->
				<div class="col-xs-9">
					<div id="search" class="input-group">
                        <div class="dropdown">
                            <form>
                                <span id="search"> <!-- Span is similar to <div> only span lets elements stay on the same line -->
     					            <input id="search-bar" name="search-bar" type="search" placeholder="Search or Hover Over Me for a List of Videos and Click" size="60" required/>
                                    <input id="search-button" name="search-button" type="submit" value="Search">
                                </span>
                            </form>
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
                                        $count = 1;
                                        $html = "";
                                        while($row = $result->fetch_assoc()) {
                                            $html = "<a href='#' id='dropdown-title-$count' class='dropdown-titles'>$row[title]</a>";
                                            echo $html;
                                            $count++;
                                        }
                                    }
                                ?>
                            </div>
                        </div>
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
                                    poster="../images/something_more.jpg"
							    	title="Something More"
							    	src="../videos/something_more.mp4"
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
						<div id="comment">
                            <form id="comment-form" action="../models/save_comment.php" method="get">
                                <span>
                                    <textarea id="comment-bar" cols="110" form="comment-form" name="comment-bar" placeholder="Add a comment..." required rows="4"></textarea>
                                    <input id="comment-button" type="submit" name="comment-button" value="Add">
                                </span>
                            </form>
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
                                    $html = "<video id='rabbit-hole-vid-$count' class='rabbit-hole-videos' controls ".
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