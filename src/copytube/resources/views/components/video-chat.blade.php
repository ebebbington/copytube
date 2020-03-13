<div id="video-chat">
    <video id="user-video" autoplay playsinline controls></video>
    <span>
        <p id="user-name"></p>
        <small><i id="user-id"></i></small>
    </span>
    <hr>
    <video id="peer-video" autoplay playsinline controls></video>
    <span>
        <p id="peer-name"></p>
        <small><i id="peer-id"></i></small>
    </span>
    <style>
        video {
            width: 100%;
            height: 250px;
        }
        span {
            display: flex
        }
        span > p {
            margin: auto 0.1em auto auto;
        }
        span > small {
            margin: auto auto auto 0.1em;
        }
    </style>
</div>
