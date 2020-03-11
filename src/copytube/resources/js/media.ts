// https://webrtc.org/getting-started/media-devices
// const { PeerServer } = require('peer');
// const server = PeerServer({port: 9000, path: '/myapp'});

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
     * Users id associated with the stream
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
        this.getUsername()
        this.generateRoomName()
        this.showMediaDevices()
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
            this.id = this.stream.id
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

    // https://peerjs.com/docs.html#start
    // https://peerjs.com/
    // PeerJS library
    //@ts-ignore - Create a peer user
    //const peer = new Peer({key: 'peerjs'}, {host: 'localhost', port: 9003, path: '/myapp'});
    // or
    const peer = new Peer(prompt('id'), { host: 'localhost', port: 9003, path: '/myapp'})
    // Get the assigned peer id when connected - this example is just gonna ask for an id so we can specifically connect to it
    let assignedPeerId = prompt('id')
    peer.on('open', function(id: string) {
        console.log('My peer ID is: ' + id);
        //assignedPeerId = id
    });
    // when we want to connect to another peer, we nee dtheir id
    var tmpotherperrid = prompt('other peer id to connect to')
    var conn = peer.connect(tmpotherperrid);
// on open will be launch when you successfully connect to PeerServer
    conn.on('open', function(){
        // here you have conn.id
        console.log('connection opened')
        conn.send('hi!');
    });
    peer.on('connection', function(conn: any) {
        console.log('peer got a connection')
        conn.on('data', function(data: any){
            // Will print 'hi!'
            console.log(data);
        });
    });

// on open will be launch when you successfully connect to PeerServer
    peer.on('open', function(data: any){
        console.log('open')
        //assignedPeerId = data
        console.log(data)
        // here you have conn.id
        // peer.send('hi!');
    });
    peer.on('connection', function(conn: any) {
        console.log('connected')
        console.log(conn)
        conn.on('data', function(data: any){
            // Will print 'hi!'
            console.log('data for connection')
            console.log(data);
        });
    });

    var call = peer.call('second', video.stream);
    call.on('stream', function(remoteStream: any) {
        // Show stream in some video/canvas element.
        console.log('call got a remote stream')
        console.log(remoteStream)
        peerVideoElement.srcObject = remoteStream
    });

    peer.on('call', function(call: any) {
        call.answer(video.stream); // Answer the call with an A/V stream.
        call.on('stream', function (remoteStream: any) {
            // Show stream in some video/canvas element.
            console.log('got a call, then a strea')
            console.log(remoteStream)
        })
    })
})
