let Realtime = new WebSocket('ws://127.0.0.1:9008/realtime')

Realtime.onopen = function(e) {
    console.log('Realtime connection has opened')
};
Realtime.onclose = function(event) {
    if (event.wasClean) {
        console.log('Realtime connection closed')
    } else {
        console.log('Realtime connection died')
    }
};
Realtime.onerror = function(error) {
    console.error('Realtime encountered an error')
};
Realtime.onmessage = function(event) {
    try {
        const message = JSON.parse(event.data)
        switch (message.channel) {
            case 'realtime.comments.new':
                //@ts-ignore
                if (Realtime.handleNewVideoComment) Realtime.handleNewVideoComment(message)
                break
            default:
                break
        }
    } catch (err) {}
};

export default Realtime
