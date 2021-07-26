import { Rhum } from "../deps.ts";

Rhum.testPlan("tests/unit/socket_test.ts", () => {
  Rhum.testSuite("startSocketServerAndListen()", () => {
  });
  Rhum.testSuite("sendRedisMessageToSocketClients()", () => {
  });
});

Rhum.run();
