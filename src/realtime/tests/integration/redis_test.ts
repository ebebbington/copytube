// expect connection to be success full
import { assertEquals, config, Rhum } from "../../deps.ts";
import { Redis } from "../../redis.ts";
// expect redis to listen to correct channels and have subscribe to them

Rhum.testPlan("tests/integration/redis_test.ts", () => {
  Rhum.testSuite("Subscribing", () => {
    Rhum.testCase(
      "A subscriber gets a message whena publisher publishes one",
      async () => {
        const redis = await Redis.connect();
        const pub = await Redis.connect();
        const sub = await Redis.createSubscriber(redis);
        const p = (async function () {
          const it = sub.receive();
          return (await it.next()).value;
        })();
        await pub.publish("realtime.comments.new", "wayway");
        const message = await p;

        assertEquals(message, {
          channel: "realtime.comments.new",
          message: "wayway",
        });
        await sub.close();
        pub.close();
        redis.close();
      },
    );
  });
  Rhum.testSuite("Connecting", () => {
    Rhum.testCase("Connection is open, and can be pinged", async () => {
      const redis = await Redis.connect();
      assertEquals(await redis.ping(), "PONG");
      assertEquals(false, redis.isClosed);
      await redis.close();
    });
  });
});

Rhum.run();
