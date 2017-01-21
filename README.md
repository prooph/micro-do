# micro-do

[prooph/micro](https://github.com/prooph/micro) version of our famous proophessor-do PHP event-sourcing example project.

## micro?

Yes, this version of proophessor-do makes use of Microservices. The newest member of the prooph package family is prooph/micro -
a small library that emphasis a minimal set up with minimal dependencies to run a prooph backend service.

A combound of Microservices sits behind an Nginx gateway. Inter-service communication is done with rabbitMq.

This demo is in an early state. We will provide more information soon and also a skeleton repo and a cli tool, which will help you set up your
own prooph Microservices.

## Installation

For now you have to follow a few steps to get the services up. 
First you need Docker. The rest will be installed by pulling docker images and run a few commands.

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
