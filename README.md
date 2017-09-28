Nasa NEO Parser
===============

DIRECTORY STRUCTURE
-------------------

      assets/             contains assets definition
      commands/           contains console commands (controllers)
      config/             contains application configurations
      controllers/        contains Web controller classes
      models/             contains model classes
      runtime/            contains files generated during runtime
      tests/              contains various tests for the basic application
      vendor/             contains dependent 3rd-party packages
      views/              contains view files for the Web application
      web/                contains the entry script and Web resources


REQUIREMENTS
------------

The minimum requirement by this project template that your Web server supports PHP 5.4.0.

INSTALLATION
------------

If you do not have [Composer](http://getcomposer.org/), you may install it by following the instructions
at [getcomposer.org](http://getcomposer.org/doc/00-intro.md#installation-nix).

You can then install this project template using the following command:

~~~
composer global require "fxp/composer-asset-plugin:^1.3.1"
git clone https://github.com/denniselite/nasa-web-client.git
cd nasa-web-client
composer install
~~~


Or you can use the docker image:

~~~
$ docker push denniselite/nasa-php-nginx:v1
~~~

Or via `docker-compose`: Create the docker-compose file `docker-compose.yml`:

~~~
version: '2'
services:
  nasa:
    container_name: "nasa"
    image: denniselite/nasa-php-nginx:v1
    volumes:
        - ./tmp:/tmp
    ports:
        - 8080:80
    links:
        - mongodb
    depends_on:
        - mongodb

  mongodb:
    image: mongo:latest
    container_name: "mongodb"
    environment:
      - MONGO_DATA_DIR=/data/db
      - MONGO_LOG_DIR=/dev/null
    volumes:
      - ./data/db:/data/db
    ports:
      - 27017:27017
    command: mongod --smallfiles --logpath=/dev/null # --quiet
~~~

Then exec: 

~~~
$ docker-compose up -d
~~~

CONFIGURATION
-------------

### Database

Edit the file `config/mongdb.php` with real data, for example:

```php
return [
    'class' => '\yii\mongodb\Connection',
    'dsn' => 'mongodb://localhost:27017/nasa',
];
```

### Storage initialization

After database configuration you should prepare data. The simple NASA service has 2 near earth objects sets which will be build with console commands.

In the project directory execute: 

1. For last 3 days `php yii neo/init3-days-data`;
2. For all dump of NASA data storage `php yii neo/init-all-data`.

Application will be process NEOs information and save it to DB.

ROUTES
------

Application has the request-response structure. For undefined routes and some exceptions it will be error with description:

```
{
    "error": {
        "status": 404,
        "message": "Route is not found."
    },
    "response": null
}
```

For wrong parameters and unhandled exceptions it will be:

```
{
    "name": "Bad Request",
    "message": "The hazardous parameter should contains only true or false",
    "code": 0,
    "status": 400,
    "type": "yii\\web\\BadRequestHttpException"
}
```

### Supported routes

* `/` - main route, returns simple message

```
{
    "hello": "world!"
}
```

* `/neo/best-month?hazardous=(true|false)` - returns the number of month with biggest count of NEOs. The `hazardous` param is configure but required.

```
{
    "error": null,
    "response": "10"
}
```

* `/neo/best-year?hazardous=(true|false)` - returns the number of year with biggest count of NEOs. The `hazardous` param is configure but required.

```
{
    "error": null,
    "response": "2012"
}
```

* `neo/hazardous` - returns all hazardous NEO with basic information:

```
{
    "error": null,
    "response": [
        {
            "_id": "59cb2603aaf0c3ee2f177a92",
            "date": "1979-12-17",
            "is_hazardous": true,
            "name": "(1979 XB)",
            "reference": "3012393",
            "speed": 82895.208318495
        },
    ...    
```

* `/neo/fastest?hazardous=(true|false)` - returns the fastest NEO with basic information. The `hazardous` param is configure but required.

```
{
    "error": null,
    "response": {
        "_id": "59cb2634aaf0c3ee2f177d80",
        "date": "2096-06-17",
        "is_hazardous": false,
        "name": "(2004 LG)",
        "reference": "3183837",
        "speed": 340884.61071708
    }
}
```

TESTING
-------

Tests are located in `tests` directory. They are developed with [Codeception PHP Testing Framework](http://codeception.com/).
By default there are 3 test suites:

- `unit`
- `functional`

Tests can be executed by running

```
vendor/bin/codecept run
``` 

The command above will execute unit and functional tests. Unit tests are testing the system components, while functional
tests are for testing user interaction. Acceptance tests are disabled by default as they require additional setup since
they perform testing in real browser. 

### Code coverage support

By default, code coverage is disabled in `codeception.yml` configuration file, you should uncomment needed rows to be able
to collect code coverage. You can run your tests and collect coverage with the following command:

```
#collect coverage for all tests
vendor/bin/codecept run --coverage-html --coverage-xml
```

You can see code coverage output under the `tests/_output` directory.
