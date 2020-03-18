import io from 'socket.io-client'

const VideoChat = (function () {

    const socket = io('http://127.0.0.1:9009')
    const peerConnection = new RTCPeerConnection()
    let isAlreadyCalling = false

    const Methods = (function () {

        /**
         * @method handleRoom
         *
         * @description
         * Handles the event/callback of the `room` message.
         * Updates the UI, such as the user ids. The `room` event is sent each time a user joins or leaves
         *
         * @param {object}      data        Data sent back from the socket server
         * @param {string}      data.myId   Your socket id
         * @param {string[]}    data.users  List of other users in the room. Empty is no other users
         * @param {string}      data.name   Name of the socket room you're in
         *
         */
        function handleRoom (data: { myId: string, users: string[], name: string}) {
            // Display our id
            const yourIdElement = document.getElementById('your-id')
            yourIdElement.textContent = data.myId
            // Display their id and the description text
            const theirIdDescriptionElement = document.getElementById('their-id-description')
            theirIdDescriptionElement.textContent = data.users[0] ? 'Their ID: ' : 'Waiting for someone to join...'
            const theirIdElement = document.getElementById('their-id')
            theirIdElement.textContent = data.users[0]
            // If they have left e.g. no users, remove the src object
            if (!data.users.length) {
                const peerVideoElement: HTMLVideoElement = document.querySelector('video#peer-video')
                peerVideoElement.srcObject = null
            }
        }

        /**
         * @method callUser
         *
         * @description
         * Make a WebRTC call to a user using their socket id, and send it through the socket server
         *
         * @param {string} socketId The other persons socket id
         */
        function callUser(socketId: string) {
            peerConnection.createOffer().then((offer) => {
                return peerConnection.setLocalDescription(new RTCSessionDescription(offer))
            }).then(() => {
                socket.emit("call-user", {
                    offer: peerConnection.localDescription,
                    to: socketId
                });
            })
        }

        /**
         * @method handleCallMade
         *
         * @description
         * Handle the event for `call-made` from the socket. Answer the call
         *
         * @param {object}  data        The data passed back from the event
         * @param {any}     data.offer  The offer for the call
         * @param {string}  data.socket Socket id trying to call
         */
        async function handleCallMade (data: { offer: any, socket: string }) {
            await peerConnection.setRemoteDescription(
                new RTCSessionDescription(data.offer)
            );
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(new RTCSessionDescription(answer));
            socket.emit('make-answer', {
                answer,
                to: data.socket
            });
        }

        /**
         * @method handleAnswerMade
         *
         * @description
         * Handler for socket event of `answer-made`. Calls the user
         *
         * @param {object}  data            The data passed back from the event
         * @param {any}     data.answer     The answer object for the call
         * @param {string}  data.socket     Socket id trying to call
         */
        async function handleAnswerMade (data: { answer: any, socket: string }) {
            await peerConnection.setRemoteDescription(
                new RTCSessionDescription(data.answer)
            );
            if (!isAlreadyCalling) {
                callUser(data.socket);
                isAlreadyCalling = true;
            }
        }

        /**
         * @method displayMyVideoAndGetTracks
         *
         * @description
         * Display the users video and add the tracks to the peer connection
         */
        function displayMyVideoAndGetTracks () {
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
        }

        return {
            handleRoom,
            callUser,
            handleCallMade,
            handleAnswerMade,
            displayMyVideoAndGetTracks
        }

    })()

    const Handlers = (function () {

        $(document).ready(function () {

            // Must be first
            Methods.displayMyVideoAndGetTracks()

            // Listen for peer connections
            peerConnection.ontrack = function({ streams: [stream] }) {
                const remoteVideo: any = document.getElementById("peer-video");
                if (remoteVideo) {
                    remoteVideo.srcObject = stream;
                }
            };

            socket
                .on('room', Methods.handleRoom)
                // subscribe to room event to get the event
                .emit('room')
                .on('call-made', async (data: { offer: any, socket: string }) => Methods.handleCallMade(data))
                .on('answer-made', async (data: { answer: any, socket: string }) => Methods.handleAnswerMade(data))

            document.getElementById('their-id').addEventListener('click', function (event: any) {
                const theirIdElement = document.getElementById('their-id')
                Methods.callUser(theirIdElement.textContent)
            })

        })

    })()

    return {

    }

})()
