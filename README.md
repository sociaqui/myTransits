# myTransits

A simple, one endpoint API for calculating the optimal route between multiple points and saving the route to a Db. Also displaying a history of all previous transits.

* [Installation](#installation)
* [Usage](#usage)
* [Debugging](#debug)

## Installation<a name="installation"></a>

1. Create an `.env` from the `.env.dist` file in the composer directory. Adapt it according to your environment and/or needs.

    ```bash
    cp docker/config.env.dist docker/config.env
    ```

2. Build & run containers

    ```bash
    $ docker-compose build
    $ docker-compose up -d
    ```

3. Update your system host file (add local.test.task)

4. Install dependencies && update database schema

        ```bash
        $ docker-compose exec php sh
        $ composer install
        # when asked for the values of parameters in parameters.yml just use defaults unless you modified something in the project
        $ bin/console doctrine:schema:update --force
        ```

## Usage<a name="usage"></a>

Just run `docker-compose up -d`, then:

* Project homepage: visit [local.test.task](http://local.test.task)
* The only specified endpoint: GET/POST on [local.test.task/transits](http://local.test.task/transits)

## Debugging<a name="debug"></a>

* Symfony dev mode: visit [local.test.task/app_dev.php](http://local.test.task/app_dev.php)
* phpMyAdmin: visit [local.test.task:8080](http://local.test.task:8080)
* Logs (Kibana): [local.test.task:81](http://local.test.task:81)
* Logs (files location): logs/nginx and logs/symfony
* xdebug is installed, enabled, and set to run with 'XDEBUG_REMOTE_PORT'=9000 and 'XDEBUG_IDEKEY'=PHPSTORM
