<div id="comment-list">
    @if (isset($comments))
    @foreach ($comments as $comment)
    <div class="media" data-user-id="{{ $comment->user_id }}">
        <div class="media-left">
            <img src="{{ $comment->profile_picture }}" alt="{{ $comment->author }}'s profile picture">
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
    @endif
    @if (!isset($comments) || sizeof($comments) < 1)
    <p>This video has no comments</p>
    @endif
</div>
