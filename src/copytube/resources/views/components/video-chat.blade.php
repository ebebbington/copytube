<div id="video-chat">
    <video id="user-video" autoplay playsinline controls></video>
    <span>
        <p>Your ID: </p>
        <p id="your-id"></p>
    </span>
    <span>
        <p id="their-id-description">Waiting for a friend...</p>
        <button id="their-id" class="button-to-text" type="button"></button>
    </span>
    <video id="peer-video" autoplay playsinline controls></video>
    <style>
        .button-to-text {
            border: none;
            background: none;
            margin-top: 0;
            margin-bottom: 1rem;
            color: blue;
        }
        video {
            height: 200px;
        }
        #user-video {
            position: fixed;
            left: 2px;
            bottom: 2px;
        }
        #peer-video {
            width: 100%;
            height: auto;
        }
        span {
            display: flex
        }
        ul {
            display: flex;
            padding-left: 0;
            list-style-type: none;
        }
        li {
            width: 33%;
        }
    </style>
</div>
