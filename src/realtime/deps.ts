// Best practice regarding a Deno project is have a similar 'package.json' file to call your imports and then export them locally, that way there is a single point for imports


export { config } from "https://deno.land/x/dotenv@v0.2.2/dotenv.ts";

// import { assertEquals } from "https://deno.land/std/testing@v0.39.0/asserts.ts";
// const testing = {
//     test: Deno.test,
//     assertEquals: assertEquals
// }
// export {
//     testing
// }

export {
    serve
} from "https://deno.land/std@v0.39.0/http/server.ts";

export {
    connectWebSocket,
    isWebSocketCloseEvent,
    isWebSocketPingEvent,
    isWebSocketPongEvent,
    acceptWebSocket,
    WebSocket
} from "https://deno.land/std@v0.39.0/ws/mod.ts";

export { connect } from "https://deno.land/x/redis@v0.9.3/redis.ts";

export { assertEquals } from "https://deno.land/std@v0.39.0/testing/asserts.ts";
