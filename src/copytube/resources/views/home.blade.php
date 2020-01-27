@extends('layout')

@section('head')

@stop

@section('content')
    <!-- search, main video, comments -->
    <div class="col-xs-12">
        <!-- search -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8">
                <div id="search" class="input-group">
                    <input id="search-bar" class="form-control" name="search-bar" type="search" placeholder="Search or Hover Over Me for a List of Videos and Click" size="60"/>
                    <input id="search-button" class="btn btn-primary" name="search-button" type="submit">
                </div>
            </div>
        </div>
        <!-- main video -->
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-8">
                <div id="main-video">
                </div>
            </div>
        </div>
        <!-- comments -->
        <div class="row">
        </div>
    </div>
    <!-- rabbit hole videos -->
    <div class="col-xs-12">
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
