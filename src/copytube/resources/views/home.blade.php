@extends('layout')

@section('head')

@stop

@section('content')
<!-- search -->
<div class="row pardon-me">
    <div id="search" class="input-group">
        <input id="search-bar" class="form-control" name="search-bar" type="search" placeholder="Search" size="60"/>
        <button id="search-button" class="btn" name="search-button">Search</button>
        <ul id="search-bar-matching-dropdown">
        </ul>
    </div>
</div>
<!-- main video -->
<div class="row pardon-me" id="main-video-holder">
    <video title="{{ $mainVideo->title }}" poster="{{ $mainVideo->poster }}" src="{{ $mainVideo->src }}" controls>
    </video>
    <h3>{{ $mainVideo->title }}</h3>
    <p>{{ $mainVideo->description }}</p>
</div>
<!-- rabbit hole videos -->
<div class="row pardon-me">
    <div id="rabbit-hole">
    @foreach ($rabbitHoleVideos as $rabbitHoleVideo)
        <div class="rabbit-hole-video-holder">
            <video src="{{ $rabbitHoleVideo->src }}"
                title="{{ $rabbitHoleVideo->title }}"
                poster="{{ $rabbitHoleVideo->poster }}">
            </video>
            <p>{{ $rabbitHoleVideo->title }}</p>
        </div>
    @endforeach
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
    @foreach ($comments as $comment)
        <div class="media">
            <div class="media-left">
                <img src="img/lava_sample.jpg" alt="Sample.jpg">
            </div>
            <div class="media-body">
                <h4 class="media-heading">{{ $comment->author }}</h4>
                <small>{{ $comment->date_posted }}</small>
                <p>{{ $comment->comment }}</p>
            </div>
        </div>
    @endforeach
    </div>
</div>
@stop
