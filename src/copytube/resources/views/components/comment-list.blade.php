<div class="row">
    <div id="comment-list">
    @foreach ($comments as $comment)
        <div class="media">
            <div class="media-left">
                <img src="img/lava_sample.jpg" alt="{{ $comment->author }}'s profile picture">
            </div>
            <div class="media-body">
                @if ($comment->author !== "")
                <h3 class="media-heading">{{ $comment->author }}</h3>
                @endif
                <small>{{ $comment->date_posted }}</small>
                <p>{{ $comment->comment }}</p>
            </div>
        </div>
    @endforeach
    </div>
</div>