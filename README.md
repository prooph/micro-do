# micro-do

[prooph/micro](https://github.com/prooph/micro) version of our famous proophessor-do PHP event-sourcing example project.

## micro?

Yes, this version of proophessor-do makes use of Microservices. The newest member of the prooph package family is [prooph/micro](https://github.com/prooph/micro) -
a small library that emphasis a minimal set up with minimal dependencies to run a prooph backend service.

A [Docker](https://www.docker.com/) container fleet of Microservices sits behind an [nginx](https://nginx.org/en/) gateway. Inter-service communication is done with [RabbitMQ](https://www.rabbitmq.com/).

This demo is in an early state. We will provide more information soon and also a **skeleton repo** and a **cli tool**, which will help you set up your
own **prooph Microservices**.

## Installation

For now you have to follow a few steps to get the services up. 
First you need [Docker](https://www.docker.com/). The rest will be installed by pulling docker images and run a few commands.

Please Note: Some commands look very complex at the moment. We will reduse complexitity as soon as possible.

### Shared package

```
$ cd packages/shared
$ docker run --rm -it --volume $(dirname $SSH_AUTH_SOCK):$(dirname $SSH_AUTH_SOCK) -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK --volume $(pwd):/app prooph/composer:7.1 install
$ cd ../..
```

### User Write Service

```
$ cd service/user-write
$ docker run --rm -it --volume $(dirname $SSH_AUTH_SOCK):$(dirname $SSH_AUTH_SOCK) -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK --volume $(pwd):/app --volume $(pwd)/../../lib:/lib prooph/composer:7.1 install
$ cd ../..
```

### Run the container fleet

```
$ docker-compose up -d
```

### Play around with the API

You'll find a `micro-do.postman_collection.json` in the root of the repo. You can import this collection into [Postman](https://www.getpostman.com/)
to send request to the different backend service. Just play around with it.

Note: The nginx gateway routes a `POST /api/v1/user/` request to the `user-write` backend service and a `GET /api/v1/user/` request to the `user-read` service.


## The Future

- cli tool to add and manage services
    - `$ prooph micro:service:add --path /api/v1/todo/ --method POST todo-write` should do the following:
        - add entry in `docker-compose.yml`
        - add entry in `gateway/www.conf`
        - add service skeleton using `todo-write` as folder name under `service`
            - skeleton should contain composer.json with namespace set up (application ns should be picked from a root config file)
            - a `public` folder with an `index.php` echoing a "Hello todo-write"
            - a `src` folder
        - issue command `$ docker-compose up -d php-todo-write`
        - issue command `$ docker-compose kill -s HUP nginx` to let Nginx reload the gateway config without downtime <3
     - `$ prooph micro:service:require|install|update todo-write <composer arguments>` proxy command for composer, it should change the working dir to `service/todo-write` and run composer with specified composer args
     - similar commands for process managers and projections
 
- Management Dashboard
    - default service: event-store-http-api
    - default service: event-store-ui
    - default service: pojections monitoring
    - default service: process manager monitoring
    
- Docker Swarm and Kubernetes showcase
    
And more cool stuff!!!
