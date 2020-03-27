// TODO 4 :: Rework this whole aspect to support the handling of redis connections and sending of socket messages to the client
import SocketServer from "./server.ts";
import { connect } from "./deps.ts";

const redis = await connect({
    hostname: "copytube_redis",
    port: 6379
});
const io = new SocketServer();

io.on('connection', () => {
    console.log('A user connected.');
});
io.on('chatroom1', function (incomingMessage: any) {
    console.log('message from chatroom1')
    io.to('chatroom1', incomingMessage);
});
io.on('disconnect', () => {
    console.log('A user disconnected.');
});


class Redis {
    public static async listen () {
        const sub = await redis.subscribe('realtime.comments.new');
        (async () => {
            for await (const { channel, message } of sub.receive()) {
                // on message
                console.log('MESSAGE RECEIEVD FROM CLASS:')
                console.log(message)
                // TODO :: Call socket method to send. eg `io.send(channel, message)`
            }
        })();
    }
    private static async playground () {
        const ok = await redis.set("hoge", "fuga");
        const fuga = await redis.get("hoge");
    }
}
await Redis.listen()