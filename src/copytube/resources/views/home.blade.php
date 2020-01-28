@extends('layout')

@section('head')

@stop

@section('content')
<!-- search -->
<div class="row pardon-me">
    <div id="search" class="input-group">
        <input id="search-bar" class="form-control" name="search-bar" type="search" placeholder="Search or Hover Over Me for a List of Videos and Click" size="60"/>
        <input id="search-button" class="btn btn-primary" name="search-button" type="submit">
    </div>
</div>
<!-- main video -->
<div class="row pardon-me" id="main-video-holder">
    <video title="Something More" poster="img/something_more.jpg" src="videos/something_more.mp4" controls>
    </video>
    <h3>Something More</h3>
    <p>Sommin'?</p>
</div>
<!-- rabbit hole videos -->
<div class="row pardon-me">
    <div id="rabbit-hole">
        <div class="rabbit-hole-video-holder">
            <video src="videos/lava_sample.mp4" title="Lava Sample" poster="img/lava_sample.jpg"></video>
            <p>Lava Sample
        </div>
    </div>
</div>
<!-- comment -->
<div class="row">
    <div id="comment">
        <span class="flex">
            <textarea contenteditable="true" type="text" class="form-control" placeholder="Add a Comment"></textarea>
            <p>0</p>
        </span>
        <button type="button" class="btn">Send</button>
    </div>
</div>
<!-- list of comments -->
<div class="row">
    <div id="comment-list">
        <div class="media">
            <div class="media-left">
                <img src="img/lava_sample.jpg" alt="Sample.jpg">
            </div>
            <div class="media-body">
                <h4 class="media-heading">Author</h4>
                <p>A comment</p>
            </div>
        </div>
    </div>
</div>
@stop
