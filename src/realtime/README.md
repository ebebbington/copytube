# Realtime (Realtime Socket Update)

This section provides the real time update aspect for **the whole application**
minus the Video Chat.

It contains and uses the following:

- Deno

  - Deno's Own WebSocket implementation

- Redis (to publish events through the websocket)

# Directory Structure / Description

- `.env`

  - Environmental variables

- `app.ts`

  - Entry point script. Calls the socket initiation and starts the redis
    subscribing process

- `deps.ts/`

  - Holds dependencies

- `README.md`

  - `this`

# Tools Used

This is the list of all tools used here, which also act as the tools learnt, or
tools implemented to learn:

- Deno

- Redis

  - Pub Sub

# Building

Handled inside the docker compose file. Don't currently have a way to rebuild
until Denon is sorted out. Instead you can do:
`docker-compose restart copytube_realtime`

# Unit Tests

## Writing the Tests

## Running the Tests

# Information

# Help
