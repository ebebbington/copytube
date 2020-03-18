<div id="video-chat">

    <video id="user-video" autoplay playsinline controls></video>
    <span>
        <button id="call-user" class="success" type="button">Waiting for a friend...</button>
        <button id="end-call" class="error hide" type="button">End Call</button>
    </span>
    <hr>
    <video id="peer-video" autoplay playsinline controls></video>
    <style>
        button {
            width: 50%;
            margin: auto;
            color: white;
            border-radius: 100px;
            font-size: 1.2em;
            height: 38px;
        }
        button.success {
            background: green;
        }
        button.error {
            background: red;
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
    </style>
</div>
