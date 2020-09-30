import { assertEquals, deferred } from "../deps.ts";
import Redis from "../redis.ts";
import SocketServer from "../socket.ts";

// Deno.test({
//     name: 'The Socket Server Should Add to the List Of Clients',
//     async fn(): Promise<any> {
//         let allClients = SocketServer.getAllClients()
//         assertEquals(0, allClients.length)
//         console.log('connecting from test')
//         const client1 = await connectWebSocket("ws://127.0.0.1:9008/realtime");
//         allClients = SocketServer.getAllClients()
//         console.log('connected, heres list:')
//         console.log(allClients)
//         assertEquals(1, allClients.length)
//     }
// })

// Deno.test({
//     name: 'The Socket Server Should Remove from The List Of Clients',
//     async fn(): Promise<any> {
//         const client1 = await connectWebSocket("ws://127.0.0.1:9008/realtime");
//         let allClients = SocketServer.getAllClients()
//         assertEquals(1, allClients.length)
//         await client1.close()
//         assertEquals(0, allClients.length)
//     }
// })

Deno.test({
  name: "The Socket Server Should Recieve a message from redis and Send the message to the Client",
  async fn(): Promise<void> {
    // Create the socket client
    const promise = deferred();
    const client = new WebSocket("ws://127.0.0.1:9008/realtime");
    const pub = await Redis.connect();
    const msgToSend = "Hello from Client :)";
    // listen for recieved messages when they come in
    client.onmessage = function (msg) {
      assertEquals(msgToSend, msg.data);
      client.close();
    };
    client.onclose = function () {
      pub.close()
      promise.resolve();
    };
    // Sennd a message through redis so the socket server can send it to us
    await pub.publish("realtime.comments.new", msgToSend);
    await promise;
  },
});

// Deno.test({
//   name: "The Socket Server Should Send the Same Message to All Clients",
//   async fn(): Promise<void> {
//     // Create the socket client
//     const promise = deferred();
//     const client1 = new WebSocket("ws://127.0.0.1:9008/realtime");
//     const client2 = new WebSocket("ws://127.0.0.1:9008/realtime");
//     const pub = await Redis.connect();
//     const msgToSend = "Hello from Client1 :)";
//     // listen for recieved messages when they come in
//     client2.onmessage = function (msg) {
//       assertEquals(msgToSend, msg.data);
//       client2.close();
//     };
//     client2.onclose = function () {
//       pub.close()
//       client1.close();
//       promise.resolve();
//     };
//     // Sennd a message through redis so the socket server can send it to us
//     await pub.publish("realtime.comments.new", msgToSend);
//     await promise;
//   },
// });
