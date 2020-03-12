@extends('layout')

@section('head')

@stop

@section('content')
    @component('components/video-chat')
    @endcomponent
    @component('components/voice-chat')
    @endcomponent
    <script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
    <script src="https://unpkg.com/peerjs@1.0.0/dist/peerjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/peerjs@0.3.20/dist/peer.min.js"></script>
@stop