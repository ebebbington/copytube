import { Rhum } from "../../deps.ts";

Rhum.testPlan("tests/unit/socket_test.ts", () => {
  Rhum.testSuite("getAllClients()", () => {
    // Rhum.testCase("Returns the list of clients", async () => {
    //   const promise = deferred()
    //   let allClients = SocketServer.getAllClients()
    //     Rhum.asserts.assertEquals(0, allClients.length)
    //     const client1 = new WebSocket("ws://127.0.0.1:9008/realtime");
    //   client1.onopen = function () {
    //     allClients = SocketServer.getAllClients()
    //     console.log(allClients)
    //     Rhum.asserts.assertEquals(allClients.length, 1)
    //     client1.close()
    //   }
    //   client1.onclose = function () {
    //     promise.resolve()
    //   }
    //   await promise;
    // })
  });
  Rhum.testSuite("startSocketServerAndListen()", () => {
  });
  Rhum.testSuite("sendRedisMessageToSocketClients()", () => {
  });
});

Rhum.run();
