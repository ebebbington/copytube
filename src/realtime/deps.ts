// Best practice regarding a Deno project is have a similar 'package.json' file to call your imports and then export them locally, that way there is a single point for imports

export { config } from "https://deno.land/x/dotenv@v3.1.0/mod.ts";

// import { assertEquals } from "https://deno.land/std/testing@v0.39.0/asserts.ts";
// const testing = {
//     test: Deno.test,
//     assertEquals: assertEquals
// }
// export {
//     testing
// }

export { connect } from "https://deno.land/x/redis@v0.25.2/redis.ts";
export type {
  Redis as IRedis,
  RedisSubscription,
} from "https://deno.land/x/redis@v0.25.2/mod.ts";
