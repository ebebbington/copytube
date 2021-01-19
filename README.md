<p align="center">
  <h1 align="center">CopyTube</h1>
</p>
<p align="center">
  <a href="https://github.com/ebebbington/copytube/actions">
    <img src="https://img.shields.io/github/workflow/status/ebebbington/copytube/master?label=ci">
  </a>
  <a href="https://github.com/ebebbington/copytube/actions">
    <img src="https://img.shields.io/github/workflow/status/ebebbington/copytube/CodeQL?label=CodeQL">
  </a>
  <a href="https://sonarcloud.io/dashboard?id=ebebbington_copytube">
    <img src="https://sonarcloud.io/api/project_badges/measure?project=ebebbington_copytube&metric=alert_status">
  </a>
  <a href="https://img.shields.io/badge/Backend%20coverage-100%25-green">
    <img src="https://img.shields.io/badge/Backend%20coverage-100%25-green">
  </a>  
</p>

---

*(Note this is a learning project)*

CopyTube is an impersonation of YouTube, utilising videos, comments, a login system and sessions. This project provides an application that uses the tools below so it can act as a training ground to further develop knowledge in those tools.

* Docker

* Linux

* Nginx

* MySQL

* PHP
    * Laravel
    * Blade

* Deno
    
* Redis
    * DB Caching
    * Pub/Sub
    
* Testing
    * Unit
    * Feature
    * Browser (Laravel Dusk, Selenium container)
    
# Ports

Nginx: 9002

PHP-FPM: 9000

Realtime: 9008

Redis: 6379

SQL: 3007

Selenium: 4444
    
# Features

* Database Caching inside PHP using Redis

* Laravel Authorisation

* Realtime updates using Laravel jobs, Redis and a Deno WebSocket

* Testing all around
    
# CI

CI is setup for the following:

    * Testing
        * Run all tests for each app (realtime, copytube and socket)
            * The respective workflows start the environment and run the tests for the container
            
    * Upgrader
        * Checks and upgrades all dependencies each week

# Containers

CopyTube is currently split into many applications (with the use of Nginx handling PHP-FPM and passing requests to the PHP container):

## Copytube (Main App)

This is the main application that holds the majority of this project, such as the views, database querying, PHP files, javascripts etc.
It's the brain essentially.

See the `README.md` for the main app [here](https://github.com/ebebbington/copytube/blob/master/src/copytube/README.md)

## SQL

This is our database for the application, automatically seeded in the docker process

## Realtime

This is our realtime socket implementation. Similar to the Socket component, but also different in that this
is responsible for giving realtime updates for users. For example, a new comment for a video is posted. This
is sent through Redis, the Realtime app listens and retrieves the message, and sends it to the clients of the realtime
connection.

I should note that although this is a web socket server, it does not receive any events or messages. It only
sends down what redis brings.

See the `README.md` for the Realtime app [here](https://github.com/ebebbington/copytube/blob/master/src/realtime/README.md)

## Redis

Redis is included inside this application. It is used by Laravel for caching database queries, and to send messages using
a Pub/Sub architecture to the Realtime app, from the Laravel app to give realtime updates

## Nginx

Our proxy server for handling PHP requests to be passed to the PHP-FPM process, and passes all requests to the PHP container

## Selenium

Container to aid in browser testing for the copytube phpfpm component, more specifically to be used with Laravel Dusk, as
it has troubles on its own running inside a docker container

# Prerequisites

Have Docker installed. This can be for Windows or for Mac - as long as you have Docker accessible in the command line. Docker knowledge is also essential to know commands such as `docker-compose down` and `docker system prune`.

## Ports

Make sure all the above ports are open

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

# Built With

* [PHP](http://www.php.net) - Server Side Language
* [Nginx](https://nginx.com) - Proxy server
* [Docker](https://docker.com) - Used for Building the Environment

# Authors

* **Edward Bebbington** - *Initial work* - [Place website name here](Place website url here)

# Acknowledgments

* Thanks to Adam for helping me learn all that I know that ended up turning the knowledge gained into this project
