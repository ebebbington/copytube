FROM debian:stable-slim

RUN apt update -y \
  && apt clean \
  && apt install bash curl unzip -y

RUN curl -fsSL https://deno.land/x/install/install.sh | DENO_INSTALL=/usr/local sh -s v1.14.0 \
  && export DENO_INSTALL="/root/.local" \
  && export PATH="$DENO_INSTALL/bin:$PATH"