import {
  acceptWebSocket,
  isWebSocketCloseEvent,
  serve,
  WebSocket,
} from "./deps.ts";
import IClient from "./interfaces/client.ts";
import { config } from "./deps.ts";

let allClients: Array<IClient> = [];
const port = config().PORT;

class SocketServer {
  public static getAllClients() {
    return allClients;
  }

  // Blocks the event loop, needs to be at the end
  // Start the websocket server, and with each connection, append to a list of clients
  // And on each disconnect, remove the client from the list
  public static async startSocketServerAndListen() {
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
        console.log("new client has just joined, heres the updated list:");
        console.log(allClients);
        try {
          for await (const ev of sock) {
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

  public static async sendRedisMessageToSocketClients(message: string) {
    try {
      // If no clients have joined then dont send - acts as an error handler as error will be thrown: forEach of undefined
      if (!allClients || !allClients.length) return false;
      allClients.forEach((client) => {
        console.info("Emitting socket message to id " + client.id);
        client.socket.send(message);
      });
    } catch (err) {
      console.error(err);
    }
  }
}

export default SocketServer;
