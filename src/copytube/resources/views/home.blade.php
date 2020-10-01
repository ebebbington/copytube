@extends('layout')

@section('head')

@stop

@section('content')
<!-- search -->
<div class="row pardon-me">
    @component('components/video-search')
    @endcomponent
</div>
<!-- rabbit hole videos -->
<div class="row pardon-me">
    @if ($videos !== '' || $videos || !empty($videos))
    @component('components/rabbit-hole', ['rabbitHoleVideos' => $videos])
    @endcomponent
    @endif
</div>
@stop
