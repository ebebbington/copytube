// expect connection to be success full
import { assertEquals } from "../deps.ts";
import { Redis } from "../../redis.ts";
// expect redis to listen to correct channels and have subscribe to them

Deno.test(
  "int/redis | Subscribing | A subscriber gets a message whena publisher publishes one",
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
    await sub.close();
    pub.close();
    redis.close();
    assertEquals(message, {
      channel: "realtime.comments.new",
      message: "wayway",
    });
  },
);

Deno.test("int/redis | Connecting | Connection is open, and can be pinged", async () => {
  const redis = await Redis.connect();
  assertEquals(await redis.ping(), "PONG");
  assertEquals(false, redis.isClosed);
  await redis.close();
});
