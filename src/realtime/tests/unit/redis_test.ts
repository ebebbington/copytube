import { Rhum } from "../../deps.ts";
import { Redis } from "../../redis.ts";

Rhum.testPlan("tests/unit/redis_test.ts", () => {
  Rhum.testSuite("connect()", () => {
    Rhum.testCase("Can connect to our Redis container", async () => {
      const redis = await Redis.connect();
      Rhum.asserts.assertEquals(redis.isConnected, true);
      redis.close();
    });
  });
  Rhum.testSuite("createSubscriber()", () => {
    Rhum.testCase("Can create a subscriber", async () => {
      const redis = await Redis.connect();
      const sub = await Redis.createSubscriber(redis);
      Rhum.asserts.assertEquals(sub.isClosed, false);
      redis.close();
    });
  });
  Rhum.testSuite("listen()", () => {
    Rhum.testCase(
      "Will call the callback when a message is sent to redis",
      async () => {
        const redis = await Redis.connect();
        const sub = await Redis.createSubscriber(redis);
        const pub = await Redis.connect();
        const p = (async function () {
          const it = sub.receive();
          return (await it.next()).value;
        })();
        await pub.publish("realtime.comments.new", "wayway");
        const message = await p;
        Rhum.asserts.assertEquals(message, {
          channel: "realtime.comments.new",
          message: "wayway",
        });
        redis.close();
        pub.close();
      },
    );
  });
});

Rhum.run();
