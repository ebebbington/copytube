import { config, connect } from "./deps.ts";

class Redis {
  public static async connect() {
    const redis = await connect({
      hostname: config().REDIS_HOST,
      port: parseInt(config().REDIS_PORT),
    });
    return redis;
  }
  public static async createSubscriber(redis: Redis) {
    const channels = ["realtime.comments.new", "realtime.users.delete"];
    console.info(
      "Subscribed to redis and awaiting messages on the following channels:",
    );
    console.info(channels);
    return await redis.subscribe(...channels);
  }
  // deno-lint-ignore allow-no-explicit-any
  public static async listen(
    sub: any,
    sendMessageCallback: (message: string) => void,
  ) {
    (async () => {
      for await (const { channel, message } of sub.receive()) {
        console.info(
          "Received a message from redis on the following channel: " + channel +
            ". Sending the message to the socket client",
        );
        console.info("FYI, here's the data received from Redis:");
        console.info(message);
        sendMessageCallback(message);
      }
    })();
  }
}

export default Redis;
