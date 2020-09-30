/**
 * Example For Handling on the Client
 *
 * socket.onmessage = function (event) { const message = JSON.parse(event.data) }
 */
import { config, connect, serve, acceptWebSocket, WebSocket } from "./deps.ts";
import Redis from "./redis.ts";
import SocketServer from "./socket.ts";

// Subscribe to redis channels and handle events when they come in
const redis = await Redis.connect();
const sub = await Redis.createSubscriber(redis);
await Redis.listen(sub, SocketServer.sendRedisMessageToSocketClients);

// Start socket server
await SocketServer.startSocketServerAndListen();
