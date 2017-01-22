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

Please Note: Some commands look very complex at the moment. We will reduce complexitity as soon as possible.

### Shared package

```
$ cd packages/shared
$ docker run --rm -it --volume $(dirname $SSH_AUTH_SOCK):$(dirname $SSH_AUTH_SOCK) -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK --volume $(pwd):/app prooph/composer:7.1 install
$ cd ../..
```

### User Write Service

```
$ cd service/user-write
$ docker run --rm -it --volume $(dirname $SSH_AUTH_SOCK):$(dirname $SSH_AUTH_SOCK) -e SSH_AUTH_SOCK=$SSH_AUTH_SOCK --volume $(pwd):/app --volume $(pwd)/../../packages:/packages prooph/composer:7.1 install
$ cd ../..
```

### Run the container fleet

```
$ docker-compose up -d
```

### Play around with the API

You'll find a `micro-do.postman_collection.json` in the root of the repo. You can import this collection into [Postman](https://www.getpostman.com/)
to send requests to the different backend services. Just play around with it.

Note: The nginx gateway routes a `POST /api/v1/user/` request to the `user-write` backend service and a `GET /api/v1/user/` request to the `user-read` service.

## FAQ

### No http routing on PHP side?

Yes, well observed! The best friend of PHP is a solid webserver. Normally you configure your webserver to forward
every request to your monolithic PHP application. Your favorite web framework takes over the request and routes it through the
framework application. While this works nice for monolithic applications running on dedicated servers it is not the best approach for
Microservices. Microservices should be small with very few dependencies. They should also be stateless so that you can scale each
service individually. 

The idea of the prooph/micro stack is that the webserver handles the http part completly and routes requests to different Microservices.
Long story, short: we use the super fast and battle-tested webserver [nginx](https://nginx.org/en/) to take over the http work.
Nginx can reload its configuration with zero-downtime. You can route requests using regex patterns to inspect the path and/or inspect other parts
of a http request, like headers or the http method. There is really no need to do that with PHP.

Without the need to route http requests on PHP side we can get rid of a lot of problems.
You know PHP's shared nothing architecture and its limitations? Yes, you know them! 
**But**, the shared nothing architecture has also a very big advantage and this is **simplicity**.
You don't need to worry about threads, non blocking I/O and async programming. Also in most cases you don't run
into memory leaks and other issues caused by long-running processes. 

The problem begins however, when you have a large codebase with hundreds of thousands of classes spread accross your
monolithic application but you don't want to load all classes on every request. The obvious solution is using an autoloader.
Next step is to use a dependency injection container which knows all your different parts of the application and
initializes controllers, services, model classes and many more on demand so that every request only causes the
initialization of the objects that are really needed to handle the request.

**Dependencies are the root of the evil**. Well, third-party packages are very very useful. You don't want (and should)
reinvent the wheel just to avoid a dependency to an open source third-party library. You get many adavantages when using open source
so it would be bad to stop doing it.
 
But a lot of different dependencies used in a monolithic codebase tend to become a very big problem, especially in PHP applications.
That is the reason why we split an application in Microservices. 

**Each service is standalone** and can define its own needs, not only third-party libraries can be installed per service but you can
also run different PHP versions with different settings and extensions installed. **The possibilities are endless!**


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
    - default service: projections monitoring
    - default service: process manager monitoring
    
- Docker Swarm and Kubernetes showcase
    
And more cool stuff!!!
