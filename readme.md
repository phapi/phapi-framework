# Phapi
Phapi is a PHP based framework aiming at rapid and simplified API development as well as focusing at performance and keeping the code base small and simple.

### What's new in version 2
This version is [PSR-7 Http Message]() compliant and takes full advantage of that fact by relying on [middleware](https://github.com/phapi/pipeline) for almost every aspect of the framework. Error handling, routing, responding to the client as well as many other functions are all in fact middleware.

There is an [Dependency injection container](https://github.com/phapi/di) that's mainly used for configuration. Each endpoint has access to both the container as well as the [https://github.com/phapi/http](request and response) objects.

## PHP versions
Phapi is currently tested on PHP 5.6, PHP 7 and HHVM. The default configuration works on all three versions but there are individual extra packages that does not support all versions. Here are a current list of packages with tests that does not pass on one or more versions:

- [phapi/cache-memcache](https://github.com/phapi/cache-memcache) - The memcached extension has not yet been updated for PHP 7.

## Installation
Install the Phapi framework via [Packagist](https://packagist.org) and [Composer](https://getcomposer.org).

```shell
$ composer require phapi/phapi-framework:2.*
```

## Configuration
See the [documentation](http://phapi.github.io/started/configuration/) for more information about configuration options and how to add extra packages.

## Documentation
The documentation can be found at [http://phapi.github.io/](http://phapi.github.io/).

## License
Phapi is licensed under the MIT License - see the [license.md](https://github.com/phapi/phapi/blob/master/license.md) file for details

## Contribute
Contribution, bug fixes etc are [always welcome](https://github.com/phapi/phapi/issues/new).
