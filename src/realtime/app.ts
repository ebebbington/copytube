// TODO 4 :: Rework this whole aspect to support the handling of redis connections and sending of socket messages to the client
import SocketServer from "./server.ts";
import { connect } from "./deps.ts";
import { serve } from "https://deno.land/std/http/server.ts";
import {
    acceptWebSocket,
    isWebSocketCloseEvent,
    isWebSocketPingEvent,
    WebSocket
} from "https://deno.land/std/ws/mod.ts";

const port = "9008";
const redis = await connect({
    hostname: "copytube_redis",
    port: 6379
});
let allClients: any = []

// const io = new SocketServer();
// io.addListener('chatroom1', )
// io.on('connection', async () => {
//     console.log('A user connected.');
//     const clients = io.getClients()
//     console.log(clients)
//     Object.keys(clients).forEach((id: string) => {
//         io.to(id, 'HI FROM SERVER')
//     })
// });
// io.on('chatroom1', function (incomingMessage: any) {
//     console.log('message from chatroom1')
//     io.to('chatroom1', incomingMessage);
// });
// io.on('disconnect', () => {
//     console.log('A user disconnected.');
// });

function sendRedisMessageToSocketClients (message: any) {
    try {
        message = JSON.stringify(message)
    } catch (err) {}
    allClients.forEach((client: any) => {
        client.socket.send('Sending message to ' + client.id + ' with data of ' + message)
    })
}
async function subscribeToRedis () {
    const channels = ['realtime.comments.new']
    const sub = await redis.subscribe(...channels);
    (async () => {
        for await (const { channel, message } of sub.receive()) {
            // on message
            console.log('MESSAGE RECEIEVD FROM CLASS:')
            console.log(message)
            sendRedisMessageToSocketClients(message)
        }
    })();
}
await subscribeToRedis()

// Working prototype for sending coket connections
console.log(`websocket server is running on :${port}`);
for await (const req of serve(`:${port}`)) {
    const {headers, conn} = req;
    acceptWebSocket({
        conn,
        headers,
        bufReader: req.r,
        bufWriter: req.w
    }).then(async (sock: WebSocket): Promise<void> => {
        allClients.push({id: conn.rid, socket: sock})
    })
}