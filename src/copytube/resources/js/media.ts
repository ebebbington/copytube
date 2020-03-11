// https://webrtc.org/getting-started/media-devices

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
        this.roomName = 'observable-TESTROOMNAME'
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
    const video = new Video()
    const audio = new Voice()
    await video.getPermissions()
    await audio.getPermissions()
    console.log(video)
    console.log(audio)
    const userVideoElement: HTMLVideoElement = document.querySelector('video#user-video')
    const peerVideoElement: HTMLVideoElement = document.querySelector('video#peer-video')
    const userVoiceElement: HTMLAudioElement = document.querySelector('audio#user-voice')
    const peerVoiceElement: HTMLAudioElement = document.querySelector('audio#peer-voice')
    userVideoElement.srcObject = video.stream;
    userVideoElement.play
    userVoiceElement.srcObject = audio.stream


    const pc1 = new RTCPeerConnection();
    const pc2 = new RTCPeerConnection()
    video.stream.getTracks().forEach((track) => {
        pc1.addTrack(track, video.stream);
    });

    //@ts-ignore
    const peer: any = new Peer()
    var conn = peer.connect('another-peers-id');
// on open will be launch when you successfully connect to PeerServer
    conn.on('open', function(){
        // here you have conn.id
        conn.send('hi!');
    });
    peer.on('connection', function(conn: any) {
        conn.on('data', function(data: any){
            // Will print 'hi!'
            console.log(data);
        });
    });
    console.log(peer)
})
