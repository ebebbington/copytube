FROM nginx:alpine

# Update and install required packages
#RUN     apt-get update

COPY ./.docker/config/copytube.conf /etc/nginx/conf.d/copytube.conf

ENTRYPOINT ["nginx"]
CMD ["-g","daemon off;"]
