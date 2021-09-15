import type IClient from "./interfaces/client.ts";
import { config } from "./deps.ts";

let allClients: Array<IClient> = [];
const port = Number(config().PORT);

export class SocketServer {
  // Blocks the event loop, needs to be at the end
  // Start the websocket server, and with each connection, append to a list of clients
  // And on each disconnect, remove the client from the list
  public static async startSocketServerAndListen(): Promise<void> {
    console.info(`websocket server is running on :${port}`);
    const listener = Deno.listen({ port });
    for await (const conn of listener) {
      const httpConn = Deno.serveHttp(conn);
      for await (const e of httpConn) {
        const { socket } = Deno.upgradeWebSocket(e.request);
        socket.onopen = () => allClients.push({ id: conn.rid, socket });
        socket.onerror = () =>
          allClients = allClients.filter((client) => client.id !== conn.rid);
        socket.onclose = () =>
          allClients = allClients.filter((client) => client.id !== conn.rid);
      }
    }
  }

  /**
   * Send a message to each connected client
   *
   * @param message - The message to send
   */
  public static sendRedisMessageToSocketClients(
    message: string,
  ): void {
    try {
      // If no clients have joined then dont send - acts as an error handler as error will be thrown: forEach of undefined
      if (!allClients || !allClients.length) return;
      allClients.forEach((client, i) => {
        console.log(client);
        try {
          console.info("Emitting socket message to id " + client.id);
          client.socket.send(message);
        } catch (_e) {
          console.error(
            `Tried to send a message to a closed socket. removing ${client.id}`,
          );
          allClients.splice(i, 1);
        }
      });
    } catch (err) {
      console.error(err);
    }
  }
}
