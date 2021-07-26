<div id="comment-list">
    // TODO whenn clicking del icon, delete from db and send redis msg, and when clicking edit,makeit editableand then update db (and sendrequest to  socket)
    @if (isset($comments))
    @foreach ($comments as $comment)
    <div class="media" data-user-id="{{ $comment['user_id'] }}">
        <div class="media-left">
            <img src="{{ $comment['profile_picture'] }}" alt="{{ $comment['author'] }}'s profile picture">
        </div>
        <div class="media-body">
            <h3 class="media-heading">{{ $comment['author'] }}</h3>
            <small>{{ $comment['date_posted'] }}</small>
            <p>{{ $comment['comment'] }}</p>
        </div>
        @if ($loggedInUserId === $comment['user_id'])
        <span class="ml-4 delete-comment" data-comment-id="{{ $comment['id'] }}">&#2716;</span>
        <span class="ml-4 edit-comment" data-comment-id="{{ $comment['id'] }}">&#x270E;</span>
        @endif
    </div>
    @endforeach
    @endif
    @if (!isset($comments) || sizeof($comments) < 1)
    <p>This video has no comments</p>
    @endif
</div>
