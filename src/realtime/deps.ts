// Best practice regarding a Deno project is have a similar 'package.json' file to call your imports and then export them locally, that way there is a single point for imports

export { config } from "https://deno.land/x/dotenv@v0.5.0/dotenv.ts";

// import { assertEquals } from "https://deno.land/std/testing@v0.39.0/asserts.ts";
// const testing = {
//     test: Deno.test,
//     assertEquals: assertEquals
// }
// export {
//     testing
// }

export {
  serve,
} from "https://deno.land/std@0.71.0/http/server.ts";

export {
  isWebSocketCloseEvent,
  isWebSocketPingEvent,
  isWebSocketPongEvent,
  acceptWebSocket,
  WebSocket,
} from "https://deno.land/std@0.71.0/ws/mod.ts";

export { connect } from "https://deno.land/x/redis@v0.13.0/redis.ts";
export type { Redis as IRedis } from "https://deno.land/x/redis@v0.13.0/mod.ts";

export { assertEquals } from "https://deno.land/std@0.71.0/testing/asserts.ts";

export {
  deferred,
} from "https://deno.land/std@0.71.0/async/deferred.ts";
