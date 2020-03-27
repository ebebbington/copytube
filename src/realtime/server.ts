import { serve } from "./deps.ts";
import {
    acceptWebSocket,
    isWebSocketCloseEvent,
    WebSocket,
} from "./deps.ts";
import { config } from "./deps.ts"
import EventEmitter from "./event_emitter.ts";

export default class SocketServer extends EventEmitter {

    private readonly host: string;

    private readonly port: string;

    constructor() {
        super()
        this.host = config().HOST
        this.port = config().PORT
        this.connect();
    }

    public async connect() {
        const server = serve(`${this.host}:${this.port}`);
        console.log('hello')
        for await (const req of server) {
            const { headers, conn } = req;
            acceptWebSocket({
                conn,
                headers,
                bufReader: req.r,
                bufWriter: req.w
            })
                .then(async (socket: WebSocket): Promise<void> => {
                    const clientId = conn.rid;
                    super.addClient(socket, clientId);
                    const it = socket.receive();
                    while (true) {
                        try {
                            const { done, value } = await it.next();
                            if (done) {
                                await super.removeClient(clientId);
                                break;
                            };
                            const ev = value;

                            if (ev instanceof Uint8Array) {
                                await super.checkEvent(ev, clientId);
                            } else if (isWebSocketCloseEvent(ev)) {
                                const { code, reason } = ev;
                                console.log("ws:Close", code, reason);
                            }
                        } catch (e) {
                            await super.removeClient(clientId);
                        }
                    }
                })
                .catch((err: Error): void => {
                    console.error(`failed to accept websocket: ${err}`);
                });
        }
    }
}