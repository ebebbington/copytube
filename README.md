# CopyTube

CopyTube is an impersonation of YouTube, utilising videos, comments, a login system and sessions. This project was created to help myself understand:
* HTML
* CSS
* JavaScript
* PHP/PHP-FPM
* Nginx

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

### Prerequisites

Have Docker installed. This can be for Windows or for Mac - as long as you have Docker accessible in the command line. Docker knowledge is also essential to know commands such as `docker-compose down` and `docker system prune`.

### Installing

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

Finally, check the Docker containers are running

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

## Running the tests

Explain how to run the automated tests for this system

### Break down into end to end tests

Explain what these tests test and why

```
Give an example
```

### And coding style tests

Explain what these tests test and why

```
Give an example
```

## Built With

* [PHP](http://www.php.net) - Server Side Language
* [Nginx](https://nginx.com) - Webserver
* [Docker](https://docker.com) - Used for Building the Environment

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Authors

* **Edward Bebbington** - *Initial work* - [Place website name here](Place website url here)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc
