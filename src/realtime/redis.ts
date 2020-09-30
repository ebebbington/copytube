import {config, connect} from "./deps.ts";
import type { IRedis} from "./deps.ts";

class Redis {
  /**
   * @returns A Redis instance
   */
  public static async connect(): Promise<IRedis> {
    const redis = await connect({
      hostname: config().REDIS_HOST,
      port: parseInt(config().REDIS_PORT),
    });
    return redis;
  }

  /**
   * Create a redis subscriber
   *
   * @param redis - a redis instance, eg `await this.connect()`
   *
   * @returns
   */
  public static async createSubscriber(redis: IRedis) {
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
