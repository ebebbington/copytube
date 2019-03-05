<?php
require_once '../../classes/models/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/copytube/classes/models/videos.php';
require_once '../../classes/models/comments.php';
// todo :: username
$user = new User();
$username = $user->username;
session_start();
if (empty($_COOKIE['sessionId1'])) {
    // Divert back to login and remove all cookies
    $user->logout();
    echo "<script>alert('Session has expired - returning to the Login screen')</script>";
    echo "<script>window.location.replace('../view/login.html')</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
    <!-- Set data before DOM -->
	<head>
		<title>CopyTube - Home</title>
		<!-- allows .js code to run jquery -->
		<script src="../../libs/jquery-3.3.1.min.js"></script>
		<!-- accesses bootstrap css files that makes css files much easier to use -->
		<link rel="stylesheet" href="../../libs/bootstrap.min.css" crossorigin="anonymous">
		<!-- accesses bootstrap js files that makes js files much easier to use -->
		<script src="../../libs/bootstrap.min.js" crossorigin="anonymous"></script>
		<!-- NOTE: My files are placed after so they overwrite the files above if needed i.e. my css > their css styles -->
        <!-- links my style sheet (.css) so it can be used -->
		<link rel="stylesheet" href="css/index.css"/>
        <!-- Link my learning script to prepare the data -->
        <script src="../../data/learning.js"></script>
        <!-- Link my javascript file so it can be used -->
        <script src="js/index.js"></script>
	</head>

	<body>
    <button id="log-out" type="button" onclick="logOut()">Log Out</button>
    <h2 id="welcome"><?php echo "$username, welcome to CopyTube"; ?></h2>
		<div class="container">
			<div class="row">
                <!-- set logo -->
				<div class="col-xs-3">
					<img id="logo" src="../../images/copytube_logo.png" alt="Error locating image"/>
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
                                    poster="../../images/something_more.jpg"
							    	title="Something More"
							    	src="../../videos/something_more.mp4"
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
                            <form id="comment-form">
                                <p id="comment-error"></p>
                                <span>
                                    <textarea id="comment-bar" cols="110" form="comment-form" name="comment-bar" placeholder="Add a comment..." required rows="4"></textarea>
                                    <input id="comment-button" type="submit" name="comment-button" value="Add" onclick="return addComment()">
                                </span>
                            </form>
    					</div>
    					<br>
                        <div id="user-comments"></div>
                        <div id="db-comments">
                            <!--
                            $comments = new Comments();
                            $allComments = $comments->getComments();
                            for ($i = 0, $l = sizeof($allComments); $i < $l; $i++) {
                                if ($allComments[$i]['title'] === 'Something More') {
                                    echo "Username: $allComments[$i]['author'] <br> Date: $allComments[$i]['dateposted'] <br> Comment: $allComments[$i]['comment'] <br> <br> <br>";
                                }
                            }
                            $db = new Database();
                            $db->closeDatabaseConnection();
                            -->
                        </div>
				</div>

                <!-- set rabbit hole -->
				<div class="col-xs-3">
					<div class="rabbit-holes col-xs-12">
                        <!--
                        $videos = new Videos();
                        $allVideos = $videos->getAllVideos();
                        $count = 1;
                        for ($i = 0, $l = sizeof($allVideos); $i < $l; $i++) {
                            if ($allVideos[$i]['title'] === 'Something More') {
                            } else {
                                echo "<video id='rabbit-hole-vid-$count' class='rabbit-hole-videos' controls ".
                                  "muted ".
                                  "poster=$allVideos[$i]['poster'] ".
                                  "title=$allVideos[$i]['title'] ".
                                  "src=$allVideos[$i]['src'] ".
                                  "width=$allVideos[$i]['width'] ".
                                  "height=$allVideos[$i]['height']>".
                                  "</video>";
                                echo "<p id='rabbit-hole-vid-$count-title' class='rabbit-hole-titles'>$allVideos[0]['title']</p>";
                            }
                            $count++;
                        }
                        $db->closeDatabaseConnection();
                        ?>
                        -->
                    </div>
				</div>
			</div>
		</div>
	</body>
</html>