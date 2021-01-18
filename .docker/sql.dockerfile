FROM mysql:5.6

# Update and install required packages
#RUN apt-get update -y

#RUN apt-get install php7.0-mysql -y
#RUN apt upgrade -y

# Copy the SQL data across so can be intitiliased
COPY    ./.docker/data/copytube-data.sql /docker-entrypoint-initdb.d/
