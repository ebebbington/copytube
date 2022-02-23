import { deferred, Rhum } from "../deps.ts";
import { Redis } from "../../redis.ts";

Deno.test("unit/redis | connect() | Can connect to our Redis container", async () => {
  const redis = await Redis.connect();
  Rhum.asserts.assertEquals(redis.isConnected, true);
  redis.close();
});
Deno.test("unit/redis | createSubscriber() | Can create a subscriber", async () => {
  const redis = await Redis.connect();
  const sub = await Redis.createSubscriber(redis);
  Rhum.asserts.assertEquals(sub.isClosed, false);
  redis.close();
});
Deno.test(
  "unit/redis | listen() | Will call the callback when a message is sent to redis",
  async () => {
    const redis = await Redis.connect();
    const pub = await Redis.connect();
    const sub = await Redis.createSubscriber(redis);
    const p = deferred<string>();
    Redis.listen(sub, (message: string) => {
      console.log("listen called");
      p.resolve(message);
    });
    await pub.publish("realtime.comments.new", "wayway");
    const message = await p;
    pub.close();
    redis.close();
    Rhum.asserts.assertEquals(message, "wayway");
  },
);
