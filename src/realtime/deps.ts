// Best practice regarding a Deno project is have a similar 'package.json' file to call your imports and then export them locally, that way there is a single point for imports

export { config } from "https://deno.land/x/dotenv@v2.0.0/dotenv.ts";

// import { assertEquals } from "https://deno.land/std/testing@v0.39.0/asserts.ts";
// const testing = {
//     test: Deno.test,
//     assertEquals: assertEquals
// }
// export {
//     testing
// }

export { serve } from "https://deno.land/std@0.99.0/http/server.ts";

export {
  acceptWebSocket,
  isWebSocketCloseEvent,
  isWebSocketPingEvent,
  isWebSocketPongEvent,
} from "https://deno.land/std@0.99.0/ws/mod.ts";
export type { WebSocket } from "https://deno.land/std@0.99.0/ws/mod.ts";

export { connect } from "https://deno.land/x/redis@v0.22.2/redis.ts";
export type {
  Redis as IRedis,
  RedisSubscription,
} from "https://deno.land/x/redis@v0.22.2/mod.ts";

export { assertEquals } from "https://deno.land/std@0.99.0/testing/asserts.ts";

export { deferred } from "https://deno.land/std@0.99.0/async/deferred.ts";

export { Rhum } from "https://deno.land/x/rhum@v1.1.10/mod.ts";
