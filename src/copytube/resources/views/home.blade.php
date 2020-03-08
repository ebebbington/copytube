@extends('layout')

@section('head')

@stop

@section('content')
<!-- search -->
@component('components/video-search')
@endcomponent
<!-- main video -->
@component('components/main-video', [
    'title' => $mainVideo->title,
    'description' => $mainVideo->description,
    'poster' => $mainVideo->poster,
    'src' => $mainVideo->src
])
@endcomponent
<!-- rabbit hole videos -->
@component('components/rabbit-hole', ['rabbitHoleVideos' => $rabbitHoleVideos])
@endcomponent
<!-- comment -->
@component('components/add-comment')
@endcomponent
<!-- list of comments -->
@component('components/comment-list', ['comments' => $comments])
@endcomponent
@stop
