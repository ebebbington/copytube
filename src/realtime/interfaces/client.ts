import type { WebSocket } from "../deps.ts";

export default interface IClient {
  id: number;
  socket: WebSocket;
}
