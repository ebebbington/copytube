<div id="rabbit-hole">
@foreach ($rabbitHoleVideos as $rabbitHoleVideo)
    <div class="rabbit-hole-video-holder">
        <video src="{{ $rabbitHoleVideo['src'] }}"
            title="{{ $rabbitHoleVideo['title'] }}"
            poster="{{ $rabbitHoleVideo['poster'] }}">
        </video>
        <p>{{ $rabbitHoleVideo['title'] }}</p>
    </div>
    @endforeach
</div>