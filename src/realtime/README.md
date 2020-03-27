# Realtime (Realtime Socket Update)

This section provides the real time update aspect for **the whole application** minus the Video Chat.

It contains and uses the following:

* Deno

* [Drash](https://github.com/drashland) [Sockets](https://github.com/drashland/sockets)

* Redis (to publish events through the websocket)

# Directory Structure / Description

* `.denonrc`

    * A server runner and file watcher [Not currently used]

* `.env`

    * Environmental variables

* `app.ts`

    * Entry point script. Calls the socket initiation and starts the redis subscribing process

* `deps.ts/`

    * Holds dependencies

* `event_emitter.ts/`

    * Handles all the event emits for our socket side of things

* `README.md`

    * `this`

* `sender/`

    * ??

* `server/`

    * The brains for the socket server

# Tools Used

This is the list of all tools used here, which also act as the tools learnt, or tools implemented to learn:

* Deno

* Web Sockets ([Drash Socket](hhttps://github.com/drashland/sockets))

* Redis

    * Pub Sub

# Building

Handled inside the docker compose file. Don't currently have a way to rebuild until Denon is sorted out

# Unit Tests

## Writing the Tests

## Running the Tests

# Information

# Help
