/**
 * Example For Handling on the Client
 *
 * socket.onmessage = function (event) { const message = JSON.parse(event.data) }
 */

import { config, connect, serve } from "./deps.ts";
import {
    acceptWebSocket,
    WebSocket
} from "./deps.ts";

interface Client {
    id: number,
    socket: WebSocket
}

const port = config().PORT;
const redis = await connect({
    hostname: config().REDIS_HOST,
    port: parseInt(config().REDIS_PORT)
});
let allClients: Array<Client> = []

function sendRedisMessageToSocketClients (message: string) {
    try {
        // If no clients have joined then dont send - acts as an error handler as error will be thrown: forEach of undefined
        if (!allClients || !allClients.length) return false
        allClients.forEach((client: any) => {
            console.info('Emitting socket message to id ' + client.id)
            client.socket.send(message)
        })
    } catch (err) {
        console.error(err)
    }
}
async function subscribeToRedis () {
    const channels = ['realtime.comments.new', 'realtime.users.delete']
    const sub = await redis.subscribe(...channels);
    console.info('Subscribed to redis and awaiting messages on the following channels:');
    console.info(channels);
    (async () => {
        for await (const { channel, message } of sub.receive()) {
            console.info('Received a message from redis on the following channel: ' + channel + '. Sending the message to the socket client')
            console.info('FYI, here\'s the data received from Redis:')
            console.info(message)
            sendRedisMessageToSocketClients(message)
        }
    })();
}

// Start subscribing to redis
await subscribeToRedis()

// Blocks the event loop, needs to be at the end
// Start the websocket server, and with each connection, append to a list of clients
// And on each disconnect, remove the client from the list
console.info(`websocket server is running on :${port}`);
for await (const req of serve(`:${port}`)) {
    const {headers, conn} = req;
    acceptWebSocket({
        conn,
        headers,
        bufReader: req.r,
        bufWriter: req.w
    }).then(async (sock: WebSocket): Promise<void> => {
        console.info('New web socket connection with socket id of: ' + conn.rid + '. Adding this connection to the list of clients')
        allClients.push({id: conn.rid, socket: sock})
        const it = sock.receive();
        while (true) {
            try {
                const {done, value} = await it.next();
                if (done) {
                    console.info('Socket connection disconnected. Removing user from client list')
                    allClients = allClients.filter((client: any) => client.id !== conn.rid)
                    break;
                }
            } catch (e) {
                console.error('Failed when trying to remove socket connection on a disconnect. Trying again but here\'s the error:')
                console.error(e)
                allClients = allClients.filter((client: any) => client.id !== conn.rid)
            }
        }
    }).catch((err: Error): void => {
        console.error(`failed to accept websocket: ${err}`);
    });
}