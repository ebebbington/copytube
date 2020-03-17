import io from 'socket.io-client'
//const socket = io('http://127.0.0.1:9009')

const VideoChat = (function () {

    const peerConnection = new RTCPeerConnection()
    let isAlreadyCalling = false

    const Methods = (function () {

    })()

    const Handlers = (function () {

    })()

    return {

    }

})()

function displayMyId (myId: string) {
    const yourIdElement = document.getElementById('your-id')
    yourIdElement.textContent = myId
}

function displayTheirId (theirId: string) {
    const theirIdDescriptionElement = document.getElementById('their-id-description')
    theirIdDescriptionElement.textContent = theirId ? 'Their ID: ' : 'Waiting for someone to join...'
    const theirIdElement = document.getElementById('their-id')
    theirIdElement.textContent = theirId
}

$(document).ready(function () {
    const socket = io('http://127.0.0.1:9009')
    // Create peer connection
    const peerConnection = new RTCPeerConnection()
    let isAlreadyCalling = false
    // Get our id
    socket.on('room', (data: { myId: string, users: string[]}) => {
        console.log(data)
        displayMyId(data.myId)
        displayTheirId(data.users[0])
    })
    socket.emit('room')
    // Method to make a call request to user
    function callUser(socketId: string) {
        peerConnection.createOffer().then((offer) => {
            return peerConnection.setLocalDescription(new RTCSessionDescription(offer))
        }).then(() => {
            socket.emit("call-user", {
                offer: peerConnection.localDescription,
                to: socketId
            });
        })
        // await peerConnection.setLocalDescription(new RTCSessionDescription(offer));
        // socket.emit("call-user", {
        //     offer,
        //     to: socketId
        // });
    }
    // On click of their id call the user
    document.getElementById('their-id').addEventListener('click', function (event: any) {
        const theirIdElement = document.getElementById('their-id')
        callUser(theirIdElement.textContent)
    })
    // When they leave reset their id element
    socket.on('remove-user', (data: { socketId: string}) => {
        const theirIdDescriptionElement = document.getElementById('their-id-description')
        theirIdDescriptionElement.textContent = 'Waiting for a friend...'
        const theirIdElement = document.getElementById('their-id')
        theirIdElement.textContent = ''
        const peerVideoElement: HTMLVideoElement = document.querySelector('video#peer-video')
        peerVideoElement.srcObject = null
    });
    // Answer a call when its created
    socket.on('call-made', async (data: any) => {
        await peerConnection.setRemoteDescription(
            new RTCSessionDescription(data.offer)
        );
        const answer = await peerConnection.createAnswer();
        await peerConnection.setLocalDescription(new RTCSessionDescription(answer));
        socket.emit('make-answer', {
            answer,
            to: data.socket
        });
    });
    // Call the suer when answered
    socket.on('answer-made', async (data: any) => {
        await peerConnection.setRemoteDescription(
            new RTCSessionDescription(data.answer)
        );
        if (!isAlreadyCalling) {
            callUser(data.socket);
            isAlreadyCalling = true;
        }
    });
    // Display stream and set tracks
    navigator.getUserMedia(
        { video: true, audio: true },
        stream => {
            const localVideo: any = document.getElementById("user-video");
            if (localVideo) {
                localVideo.srcObject = stream;
            }

            stream.getTracks().forEach(track => peerConnection.addTrack(track, stream));
        },
        error => {
            console.warn(error.message);
        }
    );
    // Listen for peer connections
    peerConnection.ontrack = function({ streams: [stream] }) {
        const remoteVideo: any = document.getElementById("peer-video");
        if (remoteVideo) {
            remoteVideo.srcObject = stream;
        }
    };
})
