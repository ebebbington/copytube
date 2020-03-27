# CopyTube (Project)

*(Note this is a learning project)*

CopyTube is an impersonation of YouTube, utilising videos, comments, a login system and sessions. This project provides an application that uses the tools below so it can act as a training ground to further develop knowledge in those tools.

* Docker

* Linux

* Nginx

* MySQL

* PHP

* Node

    * SocketIO
    
# Ports

Nginx: 9002

PHP-FPM: 9000

Socket: 9009

Realtime: 9008

Redis: 6379

SQL: 3007
    
# Features

* Database Caching inside PHP using Redis

* Realtime SocketIO chat application using Node

* Laravel Authorisation

# Components

CopyTube is currently split into 2 applications (with the use of Nginx handling PHP-FPM and passing requests to the PHP container):

## Copytube (Main App)

This is the main application that holds this whole project, such as the views, database querying, PHP files, javascripts etc.

See the `README.md` for the main app [here](https://github.com/ebebbington/copytube/blob/develop/src/copytube/README.md)

## Socket (Web Socket)

This handles all of our server-side web socket connections. For example, it handles our video chat application at `/chat`.

See the `README.md` for the Socket app [here](https://github.com/ebebbington/copytube/blob/develop/src/socket/README.md)

# Prerequisites

Have Docker installed. This can be for Windows or for Mac - as long as you have Docker accessible in the command line. Docker knowledge is also essential to know commands such as `docker-compose down` and `docker system prune`.

## Ports

Make sure ports 9000 and 9002 are open for PHP-FPM and Nginx respectively. As well as port 9009 for the socket server.

# Run the Project

Clone the repository

```
cd /your/chosen/dir
git clone https://github.com/ebebbington/copytube.git
cd copytube
```

Build and start Docker

```
docker-compose build && docker-compose up
```

Check the Docker containers are running

```
docker-compose ps
```

The output should look similar to the below

```
$ docker-compose ps
     Name                    Command              State                Ports
------------------------------------------------------------------------------------------
copytube_nginx    nginx -g daemon off;            Up      80/tcp, 0.0.0.0:9002->9002/tcp
copytube_phpfpm   docker-php-entrypoint php-fpm   Up      0.0.0.0:9000->9000/tcp
copytube_sql      docker-entrypoint.sh mysqld     Up      0.0.0.0:3007->3006/tcp, 3306/tcp
```

Finally, go to the website

* Mac
     `0.0.0.0:9002`
     
* Windows
     `127.0.0.1:9002`

# Containers

## Nginx

Our proxy server for handling PHP requests to be passed to the PHP-FPM process, and passes all requests to the PHP container

## PHP-FPM

The PHP container that has PHP already configured

## SQL

Our database container to house the database data

## Socket

Our web socket server build with NodeJS and SocketIO

## Redis

Help with caching request. In the future i intend on using it for a pub/sub

# Built With

* [PHP](http://www.php.net) - Server Side Language
* [Nginx](https://nginx.com) - Webserver
* [Docker](https://docker.com) - Used for Building the Environment

# Authors

* **Edward Bebbington** - *Initial work* - [Place website name here](Place website url here)

# Acknowledgments

* Thanks to Adam for helping me learn all that I know that ended up turning the knowledge gained into this project
