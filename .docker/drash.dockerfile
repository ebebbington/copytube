FROM debian:stable-slim

RUN apt update -y \
          && apt clean
RUN apt install bash curl unzip -y

#COPY ./.docker/config/deno-install.sh /etc/deno-install.sh
#RUN sh /etc/deno-install.sh v0.36.0
RUN curl -fsSL https://deno.land/x/install/install.sh | DENO_INSTALL=/usr/local sh -s v1.8.3

RUN export DENO_INSTALL="/root/.local"
RUN export PATH="$DENO_INSTALL/bin:$PATH"