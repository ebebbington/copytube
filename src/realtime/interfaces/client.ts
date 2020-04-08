import {WebSocket} from "../deps.ts";

interface IClient {
    id: number,
    socket: WebSocket
}

export default IClient