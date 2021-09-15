// Best practice regarding a Deno project is have a similar 'package.json' file to call your imports and then export them locally, that way there is a single point for imports

export { config } from "https://deno.land/x/dotenv@v3.0.0/mod.ts";

// import { assertEquals } from "https://deno.land/std/testing@v0.39.0/asserts.ts";
// const testing = {
//     test: Deno.test,
//     assertEquals: assertEquals
// }
// export {
//     testing
// }

export { connect } from "https://deno.land/x/redis@v0.23.2/redis.ts";
export type {
  Redis as IRedis,
  RedisSubscription,
} from "https://deno.land/x/redis@v0.23.2/mod.ts";
