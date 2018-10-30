<!DOCTYPE html>

<html>

	<head>

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

	<body>

		<div class="container">

			<div class="row">

				<!-- region Logo -->
				<div class="col-xs-3">

					<img id="logo" src="imageresources/CopyTube_Logo.png"/>

				</div>
                <!-- endregion -->

				<!-- region Search Bar/Button -->
				<div class="col-xs-9">
					<div id="search" class="input-group">
                        <div class="dropdown">
     					    <input id="search-bar" type="text" class="form-control my-input" placeholder="Hover Over Me for a List of Videos"/>
                            <div class="dropdown-content">
                                <a href='#'>Something More</a>
                                <a href='#'>Lava Sample</a>
                                <a href='#'>An Iceland Venture</a>
                            </div>
                        </div>
      					<span class="input-group-btn">
       						<button id="search-button" class="btn btn-default" type="button">Beep Boop Calculate!</button>
      					</span>
    				</div>
				</div>
                <!-- endregion -->

			</div>

			<div class="row">

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
                                    poster="imageresources/something_more.jpg"
							    	title="Something More"
							    	src="http://mazwai.com/system/posts/videos/000/000/191/original/something-more.mp4?1445788608"
							    	width="750"
							    	height="400">
							    Sorry, your browser doesn't support embedded videos.
							</video>
							<!-- main video title -->
							<p id="main-video-title">Something More</p>
							<br>
							<!-- main video description -->
							<p id="main-video-description">Watch this inspirational video as we look at all of the beautiful things inside this world</p>
						<br>
						<br>
						</div>
                        <!-- endregion -->

						<!-- region Comments Title/Count/Bar & Button -->
                        <p id="comment-title">Comments Section</p> <p id="comment-count"></p>
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
						<!-- region Display New Comment -->
                        <div id="user-comments"></div>
                        <!-- endregion -->

                        <!-- region Get Database Comments -->
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
                        <!-- endregion -->

				</div>

                <!-- region Rabbit Hole Videos -->
				<div class="col-xs-3">

					<div class="rabbit-holes col-xs-12">

                        <!-- PHP Code Displaying Rabbit Holes -->
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
                                    /*FixMe [001]: 'title' is only taking the first word of the title e.g. title="Lava" sample src="....."
                                    So, $sql is taking "Lava Sample" but when being echoed it equals "Lava"*/

                                    $html .= '<video id="rabbit-hole-vid-1" class="rabbit-hole-vid" controls ';
                                    $html .= ' muted ';
                                    $html .= 'poster=' . $row['poster'] . " ";
                                    $html .= 'title=' . $row['title'] . " ";
                                    $html .= 'src=' . $row['src'] ;
                                    $html .= 'width=' . $row['width'] ;
                                    $html .= 'height=' . $row['height'] . '>' ;
                                    $html .= '</video>';

                                    echo $html;

                                    //create and display titles
                                    echo "<p id='rabbit-hole-vid-'".$count."-title" . " class='rabbit-hole-titles>'" .$row['title']. "</p>";
                                    $count + 1;
                                }

                                echo $html;
                            }
                            //close database connection
                            $connection->close();
                        ?>

                    </div>

				</div>
                <!-- endregion -->

			</div>

		</div>

	</body>

</html>