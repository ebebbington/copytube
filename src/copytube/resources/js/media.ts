// https://webrtc.org/getting-started/media-devices
// const { PeerServer } = require('peer');
// const server = PeerServer({port: 9000, path: '/myapp'});
import io from 'socket.io-client'
const socket = io('http://127.0.0.1:9009')

class Media {

    /**
     * Configs for getUserMedia
     */
    private readonly constraints: MediaStreamConstraints

    /**
     * Has the class got access to the users media
     */
    private hasMedia: boolean

    /**
     * The users media e.g. stream
     */
    public stream: MediaStream

    /**
     *
     */
    public id: string

    /**
     * Name of the room
     */
    public roomName: string

    /**
     * Name to associate user with
     */
    public username: string

    /**
     * Sets constraints and shows the users ocnnected media devices
     *
     * @param constraints
     */
    constructor (constraints: MediaStreamConstraints) {
        this.constraints = constraints
        this.setup()
    }

    protected setup () {
        this.generateUsername()
        this.generateRoomName()
        this.generateId()
    }

    private generateId () {
        this.id = Math.random().toString(36).substr(7)
    }

    private generateUsername () {
        this.username = Math.random().toString(36).substr(7)
    }

    private generateRoomName () {
        this.roomName = 'observable-' + Math.random().toString(36).substring(7);
    }

    /**
     * Log out the users connected media devices
     */
    public showMediaDevices () {
        const types: string[] = ['audioinput', 'autooutput', 'videoinput']
        navigator.mediaDevices.enumerateDevices()
        .then((devices: any) => {
            console.log('Media devices', devices)
        })
    }

    /**
     * Get the users media
     */
    public async getPermissions () {
        try {
            this.stream = await navigator.mediaDevices.getUserMedia(this.constraints)
            this.hasMedia = true
        } catch (err) {
            console.error(`Error accessing media devices:`, err)
        }
    }
}

const configuration = {
    iceServers: [{
        urls: 'stun:stun.l.google.com:19302' // Google's public STUN server
    }]
};

const PeerConfigs = {
    host: 'localhost',
    port: 9003,
    path: '/myapp'
}

$(document).ready(async function () {
    // Display video and audio
    const ThisUser = new Media({video: true, audio: true})
    await ThisUser.getPermissions()
    const userNameElement: HTMLHeadingElement = document.querySelector('p#user-name')
    const userIdElement: any = document.querySelector('i#user-id')
    const userVideoElement: HTMLVideoElement = document.querySelector('video#user-video')
    const peerNameElement: HTMLHeadingElement = document.querySelector('p#peer-name')
    const peerIdElement: any = document.querySelector('i#peer-id')
    const peerVideoElement: HTMLVideoElement = document.querySelector('video#peer-video')
    const userVoiceElement: HTMLAudioElement = document.querySelector('audio#user-voice')
    const peerVoiceElement: HTMLAudioElement = document.querySelector('audio#peer-voice')
    userVideoElement.srcObject = ThisUser.stream;
    userVideoElement.play
    userNameElement.textContent = ThisUser.username
    userIdElement.textContent = ThisUser.id
    //userVoiceElement.srcObject = audio.stream

    // RTC Peer Connection - Native
    const pc1 = new RTCPeerConnection();
    const pc2 = new RTCPeerConnection()
    ThisUser.stream.getTracks().forEach((track) => {
        pc1.addTrack(track, ThisUser.stream);
    });

    // Peer handler when we get their id
    //@ts-ignore
    const peer = new Peer(ThisUser.id, PeerConfigs)
    peer.on('open', function (id: string) {
        console.log('Peer connection has opened. This also will show my id: ' + id);
    });
    peer.on('connection', function(conn: any) {
        console.log('peer got a connection, below is the connection param')
        console.log(conn)
        conn.on('data', function(data: any){
            console.log('Connection got some data:')
            console.log(data)
            // Will print 'hi!'
            console.log('Going to send the other connection some data')
            conn.send('Hi')
        });
        console.log('Going to send the other connection some data')
        conn.send('some test data')
    });
    peer.on('call', function (call: any) {
        console.log('peer got a call')
        call.answer(ThisUser.stream)
    })
    function connectToOtherPeer (theirId: string) {
        console.log('[connecttootherpeer]')
        console.log('heres their id: ' + theirId)
        const conn = peer.connect(theirId)
        conn.on('open', function () {
            console.log('connection to other peer is open')
            conn.on('data', function (data: any) {
                console.log('connection receieved some data: ' + data)
            })
            console.log('Going to send them from data from the conn open cb')
            conn.send('Hi!')
        })
        var call = peer.call(theirId, ThisUser.stream)
        call.on('stream', (data: any) => {
            console.log('this call got a stream!')
            peerVideoElement.srcObject = data
        })
    }

    // Socket - only to get the peer id of the joined person
    class S {
        private room: string
        public theirId: string
        public hasTheirId: boolean = false
        constructor(data: {username: string, id: string}) {
            socket.emit('user joined', {username: data.username, id: data.id})
            socket.on('user joined', this.handleUserJoined)
            this.listenForDisconnect()
        }
        private handleUserJoined (data: { username: string, id: string, room: string}) {
            console.log('[handleuserjoined')
            peerNameElement.textContent = data.username
            peerIdElement.textContent = data.id
            console.log(data)
            this.room = data.room
            this.theirId = data.id
            this.hasTheirId = true
            connectToOtherPeer(data.id)
        }

        private listenForDisconnect () {
            window.onbeforeunload = () => {
                socket.emit('user left', {id: ThisUser.id, room: this.room, username: ThisUser.username})
            }
        }
    }

    const s = new S({username: ThisUser.username, id: ThisUser.id})

    //
    // Our peer, used to bridge our users
    //
    // https://peerjs.com/docs.html#start
    // https://peerjs.com/
    // npm run dev; node_modules/.bin/peerjs --port 9003 --key peerjs --path /myapp
    // PeerJS library
    //@ts-ignore - Create a peer user
    //const peer = new Peer({key: 'peerjs'}, {host: 'localhost', port: 9003, path: '/myapp'});
    // or
    //const peer = new Peer(video.id, { host: 'localhost', port: 9003, path: '/myapp'})
    // peer.on('open', function(id: string) {
    //     console.log('Peer connection has opened. My peer ID is (which is set in object creation: ' + id);
    // });
    // start a connection with a peer
    // const otherp = peer.connect(s.theirId)
    // // receieve connection
    // peer.on('connection', function(conn: any) {
    //     console.log('peer got a connection')
    //     console.log('below is the conn param:')
    //     console.log(conn)
    //     conn.on('data', function(data: any){
    //         // Will print 'hi!'
    //         conn.send('Hi')
    //         console.log('connection got data:')
    //         console.log(data);
    //     });
    //     conn.send('some test data')
    // });
    // var testcall = peer.call(s.theirId, video.stream)
    // testcall.on('stream', (data: any) => {
    //     peerVideoElement.srcObject = data
    // })

    //
    // Socket - to handle the other users id for their peer id
    //
    // socket to keep track of ids



//     var conn = peer.connect(tmpotherperrid);
// // on open will be launch when you successfully connect to PeerServer
//     conn.on('open', function(data: any){
//         // here you have conn.id
//         console.log('connection opened')
//         console.log('heres data passed back:')
//         console.log(data)
//         conn.send('hi!');
//     });

    // peer.on('connection', function(conn: any) {
    //     console.log('connected')
    //     console.log(conn)
    //     conn.on('data', function(data: any){
    //         // Will print 'hi!'
    //         console.log('data for connection')
    //         console.log(data);
    //     });
    // });
})
