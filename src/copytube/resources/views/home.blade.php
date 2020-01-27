@extends('layout')

@section('head')

@stop

@section('content')
    <button id="log-out" type="button">Log Out</button>
    <h2 id="welcome"></h2>
    <!-- set logo -->
    <div class="col-xs-3">
        <img id="logo" src="img/copytube_logo.png" alt="Error locating image"/>
    </div>
    <!-- create search bar and button -->
    <div class="row">
        <div class="col-xs-9">
            <div id="search" class="input-group">
                <div class="dropdown">
                    <form>
                        <span id="search"> <!-- Span is similar to <div> only span lets elements stay on the same line -->
                            <input id="search-bar" class="form-control" name="search-bar" type="search" placeholder="Search or Hover Over Me for a List of Videos and Click" size="60" required/>
                            <input id="search-button" class="btn btn-primary" name="search-button" type="submit">
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
                <!--
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
                    -->
                <br>
                <br>
            </div>
            <div class="well" id="my-video-info">
            </div>

                <!-- create comments section -->
            <p id="comment-title">Comments Section</p> <p id="comment-count">0</p>
            <hr>
            <div id="comment">
                <p id="comment-error"></p>
                <span>
                    <textarea id="comment-bar" class="form-control" cols="110" form="comment-form" name="comment-bar" placeholder="Add a comment..." required rows="4"></textarea>
                    <input id="comment-button" class="btn btn-primary" type="submit" name="comment-button" value="Add">
                </span>
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
        <div class="col-xs-3 rabbit-hole-content">
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
            <p id='rabbit-hole-titles'></p>
        </div>
    </div>
@stop
