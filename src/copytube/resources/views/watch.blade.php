@extends('layout')

@section('head')

@stop

@section('content')
    <!-- search -->
    <div class="row pardon-me">
        @component('components/video-search')
        @endcomponent
    </div>
    <!-- main video -->
    <div class="row pardon-me">
        @component('components/main-video', [
            'title' => $mainVideo->title,
            'description' => $mainVideo->description,
            'poster' => $mainVideo->poster,
            'src' => $mainVideo->src
        ])
        @endcomponent
    </div>
    <!-- rabbit hole videos -->
    <div class="row pardon-me">
        @component('components/rabbit-hole', ['rabbitHoleVideos' => $rabbitHoleVideos])
        @endcomponent
    </div>
    <!-- comment -->
    <div class="row">
        @component('components/add-comment')
        @endcomponent
    </div>
    <!-- list of comments -->
    <div class="row">
        @component('components/comment-list', ['comments' => $comments])
        @endcomponent
    </div>
@stop
