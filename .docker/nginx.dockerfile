FROM nginx:latest

MAINTAINER Intercity Dev Team

# Update and install required packages
RUN     apt-get update
RUN     apt-get install vim -y

COPY ./.docker/config/copytube.conf /etc/nginx/conf.d/copytube.conf

ENTRYPOINT ["nginx"]
CMD ["-g","daemon off;"]
