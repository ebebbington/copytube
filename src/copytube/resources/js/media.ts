// https://webrtc.org/getting-started/media-devices

class Media {

    /**
     * Configs for getUserMedia
     */
    private readonly constraints: MediaStreamConstraints

    /**
     * Has the class got access to the users media
     */
    public hasMedia: boolean = false

    /**
     * The users media e.g. stream
     */
    public stream: MediaStream = null

    /**
     * Users id associated with the stream
     */
    public id: string = ''

    public roomName: string = ''

    public username: string = ''

    /**
     * Sets constraints and shows the users ocnnected media devices
     *
     * @param constraints
     */
    constructor (constraints: MediaStreamConstraints) {
        this.constraints = constraints
        this.showMediaDevices()
        this.generateRoomName()
        this.getUsername()
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
    private showMediaDevices () {
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

const configuration = {
    iceServers: [{
        urls: 'stun:stun.l.google.com:19302' // Google's public STUN server
    }]
};

$(document).ready(async function () {
    const media = new Media({video: true, audio: true})
    // Get the media
    await media.getPermissions()
    // Log the stream
    if (media.hasMedia) {
        console.log(media)
        const videoElement = document.querySelector('video#user-video');
        //@ts-ignore
        videoElement.srcObject = media.stream;
        //@ts-ignore
        videoElement.play
    }
})
