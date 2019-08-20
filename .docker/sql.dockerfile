FROM mysql:5.6

# Update and install required packages
RUN apt-get update -y
RUN apt-get install vim -y

# Copy the SQL data across so can be intitiliased
COPY    ./.docker/data/copytube-data.sql /docker-entrypoint-initdb.d/
