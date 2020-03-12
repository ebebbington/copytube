// https://webrtc.org/getting-started/media-devices
// const { PeerServer } = require('peer');
// const server = PeerServer({port: 9000, path: '/myapp'});
import io from 'socket.io-client'
const socket = io('http://127.0.0.1:9009')
abstract class Media {

    /**
     * Configs for getUserMedia
     */
    protected abstract constraints: MediaStreamConstraints

    /**
     * Has the class got access to the users media
     */
    protected abstract hasMedia: boolean

    /**
     * The users media e.g. stream
     */
    protected abstract stream: MediaStream

    /**
     *
     */
    protected abstract id: string

    /**
     * Name of the room
     */
    protected abstract roomName: string

    /**
     * Name to associate user with
     */
    protected abstract username: string

    /**
     * Sets constraints and shows the users ocnnected media devices
     *
     * @param constraints
     */
    protected constructor () {
    }

    protected setup () {
        //this.getUsername()
        this.generateRoomName()
        this.generateId()
        this.showMediaDevices()
    }

    private generateId () {
        this.id = Math.random().toString(36).substr(7)
    }

    private getUsername () {
        this.username = prompt('Username')
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

class Video extends Media {
    public constraints: MediaStreamConstraints
    public hasMedia = false
    public username = ''
    public id = ''
    public roomName  = ''
    public stream: MediaStream
    constructor() {
        super()
        this.constraints = {video: true, audio: true}
        this.setup()
    }
}

class Voice extends Media {
    public constraints: MediaStreamConstraints
    public hasMedia = false
    public username = ''
    public id = ''
    public roomName  = ''
    public stream: MediaStream
    constructor() {
        super()
        this.constraints = {video: false, audio: true};
        this.setup()
    }
}

const configuration = {
    iceServers: [{
        urls: 'stun:stun.l.google.com:19302' // Google's public STUN server
    }]
};

$(document).ready(async function () {
    // Display video and audio
    const video = new Video()
    const audio = new Voice()
    await video.getPermissions()
    await audio.getPermissions()
    const userVideoElement: HTMLVideoElement = document.querySelector('video#user-video')
    const peerVideoElement: HTMLVideoElement = document.querySelector('video#peer-video')
    const userVoiceElement: HTMLAudioElement = document.querySelector('audio#user-voice')
    const peerVoiceElement: HTMLAudioElement = document.querySelector('audio#peer-voice')
    userVideoElement.srcObject = video.stream;
    userVideoElement.play
    userVoiceElement.srcObject = audio.stream

    // RTC Peer Connection - Native
    const pc1 = new RTCPeerConnection();
    const pc2 = new RTCPeerConnection()
    video.stream.getTracks().forEach((track) => {
        pc1.addTrack(track, video.stream);
    });

    // Peer handler when we get their id
    //@ts-ignore
    const peer = new Peer(video.id, { host: 'localhost', port: 9003, path: '/myapp'})
    peer.on('open', function(id: string) {
        console.log('Peer connection has opened. My peer ID is (which is set in object creation: ' + id);
    });
    peer.on('connection', function(conn: any) {
        console.log('peer got a connection')
        console.log('below is the conn param:')
        console.log(conn)
        conn.on('data', function(data: any){
            // Will print 'hi!'
            conn.send('Hi')
            console.log('connection got data:')
            console.log(data);
        });
        conn.send('some test data')
    });
    peer.on('call', function (call: any) {
        console.log('peer got a call')
        call.answer(video.stream)
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
            conn.send('Hi!')
        })
        var call = peer.call(theirId, video.stream)
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
            console.log(data)
            this.room = data.room
            this.theirId = data.id
            this.hasTheirId = true
            connectToOtherPeer(data.id)
        }

        private listenForDisconnect () {
            window.onbeforeunload = () => {
                socket.emit('user left', {id: video.id, room: this.room, username: video.username})
            }
        }
    }

    const s = new S({username: video.username, id: video.id})

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
