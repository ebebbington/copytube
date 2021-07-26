import {
  acceptWebSocket,
  isWebSocketCloseEvent,
  serve,
  WebSocket,
} from "./deps.ts";
import type IClient from "./interfaces/client.ts";
import { config } from "./deps.ts";

let allClients: Array<IClient> = [];
const port = config().PORT;

export class SocketServer {

  // Blocks the event loop, needs to be at the end
  // Start the websocket server, and with each connection, append to a list of clients
  // And on each disconnect, remove the client from the list
  public static async startSocketServerAndListen(): Promise<void> {
    console.info(`websocket server is running on :${port}`);
    for await (const req of serve(`:${port}`)) {
      const { headers, conn } = req;
      acceptWebSocket({
        conn,
        headers,
        bufReader: req.r,
        bufWriter: req.w,
      }).then(async (sock: WebSocket): Promise<void> => {
        console.info(
          "New web socket connection with socket id of: " + conn.rid +
            ". Adding this connection to the list of clients",
        );
        allClients.push({ id: conn.rid, socket: sock });
        console.log("new client has just joined, here's the updated list:");
        console.log(allClients);
        try {
          for await (const ev of sock) {
            console.log("event form sock");
            console.log(ev);
            if (isWebSocketCloseEvent(ev)) {
              console.info(
                "Socket connection disconnected. Removing user from client list",
              );
              allClients = allClients.filter((client) =>
                client.id !== conn.rid
              );
              console.log("a client has disconned, heres the updated list:");
              console.log(allClients);
            }
          }
        } catch (err) {
          console.error(
            "Failed when trying to remove socket connection on a disconnect. Trying again but here's the error:",
          );
          console.error(err);
          allClients = allClients.filter((client) => client.id !== conn.rid);
          console.log("a client has disconned, heres the updated list:");
          console.log(allClients);
        }
      }).catch((err: Error): void => {
        console.error(`failed to accept websocket: ${err}`);
      });
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
