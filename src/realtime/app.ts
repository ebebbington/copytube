// TODO 4 :: Rework this whole aspect to support the handling of redis connections and sending of socket messages to the client
import SocketServer from "./server.ts";
const io = new SocketServer();
io.on('connection', () => {
    console.log('A user connected.');
});
io.on('chatroom1', function (incomingMessage: any) {
    io.to('chatroom1', incomingMessage);
});
io.on('disconnect', () => {
    console.log('A user disconnected.');
});

import { connect } from "./deps.ts";
const redis = await connect({
  hostname: "copytube_redis",
  port: 6379
});
const ok = await redis.set("hoge", "fuga");
const fuga = await redis.get("hoge");

// TODO 1 :: Create a conf list of channels to listen on. check if an array can be passed in
const sub = await redis.subscribe("test-channel");
(async function() {
  for await (const { channel, message } of sub.receive()) {
    // on message
    console.log('received a message through redis on channel test-channel:' + message)
  }
})();