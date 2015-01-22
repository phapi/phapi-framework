# Phapi

Phapi is a PHP based framework aiming at simplifying API development and a the same time being fast and small and not include functionality that others to better.

[![Build Status](https://travis-ci.org/ahinko/phapi.svg?branch=develop)](https://travis-ci.org/ahinko/phapi)
[![Code Climate](https://codeclimate.com/github/ahinko/phapi/badges/gpa.svg)](https://codeclimate.com/github/ahinko/phapi)
[![Test Coverage](https://codeclimate.com/github/ahinko/phapi/badges/coverage.svg)](https://codeclimate.com/github/ahinko/phapi)

## Documentation
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Logging](#logging)
5. [Cache](#cache)
6. [Serializers](#serializers)
7. [Request](#request)
8. [Trigger response and error handling](#trigger-response-and-error-handling)

### Requirements
Phapi requires PHP 5.5 or above.

### Installation
*TODO.*

**Phapi is under development and it's not ready to use yet.**

#### PHP settings
It's suggested to turn off displaying of errors in production environments and rely on logging instead since Phapi has an error handler that will display serialized error messages.

During development it's beneficial however to display errors and setting error reporting to E_ALL.

### Configuration
Configuration is easy with Phapi. Create an array and pass it to the Phapi constructor and you are done. As an example we will set up basic logging with [Monolog](https://github.com/Seldaek/monolog):
```php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('./logs/app_'. date('Y-m-d') .'.log', Logger::DEBUG));

$configuration = [
    'logWriter'     => $logger,
];

$api = new \Phapi\Phapi($configuration);

```

### Logging
See the [Configuration](#configuration) section for an example of how to configure a logger. Loggers must implement the PSR-3 LoggerInterface.

Registered logger can be accessed by using the Phapi->getLogWriter() function. In a resource the code might look like this:
```php
$this->app->getLogWriter()->debug('This code just logged this message');
```

### Cache
Phapi uses a cache if one can be found. It's used in different places, for example in the router when trying to find matching routes. Instead of having to look up the same route over and over again a cache is used to save time. This is especially good when more complex routes (with regex) is used frequently.

#### Example usage
Create a cache and add it to the configuration:

```php
// Configuration
$configuration = [
    'cache' = new \Phapi\Cache\Memcache('localhost', 11211);
];
```

Retrieve and use the cache in a Resource:

```php
// Get cache from app
$cache = $this->app->getCache();

// Set key and value
$cache->set('key1', 'some value');

// Returns true
$cache->has('key1');

// Get value
$cache->get('key1');

// Remove key and value
$cache->clear('key1');

```

#### Cache Interface
There is an interface that can be implemented if a storage type is missing. Only four functions is needed:

- **connect()** - connect to the cache server and return a boolean
- **set($key, $value)** - set a key and value
- **get($key)** - get the value based on key
- **clear($key)** - remove/clear cache based on key
- **has($key)** - check if cache has the key stored
- **flush()** - clears the cache

#### Storage types
The following storage types are included in Phapi:

- **Memache**
- **NullCache**, an empty class simulating a cache. It's used in those cases where no cache is configured. This simplifies using the cache functions since we don't need to check if a cache actually exists.

### Serializers
Serializers have two tasks: unserialize response body and serialize response body. The default configuration sets everything up for the serializers that's included in Phapi. This can of course be changed by doing some configuration:

```php
$configuration = [
  'defaultAccept' => 'application/json',
  'serializers' => [
    new Json(['application/vnd.phapi+json'], ['text/html']),
    new Jsonp(['application/vnd.phapi+javascript']),
    new FormUrlEncoded(),
  ]
];

$api = new \Phapi\Phapi($configuration);
```

Use the **defaultAccept** configuration to specify the type used if the client asks for a type that aren't supported.

It is possible to add more supported content types as you can see in the example above. The Json serializer accepts **application/vnd.phapi+json** for both serialization and unserialization. The second parameter, **text/html** indicates that the Json serializer should be used if the client has sent an **Accept** header with the content text/html. However, the Json serializer will NOT unserialize request bodies with the **Content-Type** header set to text/html.

#### Implementing serializers
Serializers must extend the abstract **Phapi\Serializer** class and implement the **serialize()** and **unserialize()** functions.

### Request
Retrieve and use the Request object in a Resource:

```php
// Get request
$request = $this->app->getRequest();

// Get headers
$headers = $request->getHeaders();

// Get the Origin header
$origin = $request->getHeaders()->get('origin');
```

There are four types of parameters that can be retrieved from the Request object:

- **get**, usually populated by the global $_GET variable.
- **post**, usually populated by the global $_POST variable.
- **server**, usually populated by the global $_SERVER variable.
- **attributes**, attributes are discovered via decomposing the URI path. Example: http://localhost/users/phapi where phapi are the username of a user.

These four types are all stored in a Phapi\Bucket object and can there for be accessed and used in the same way:

```php
// Get query (GET) parameters
$query = $request->getQuery(); // Returns a Phapi\Bucket object

// Check if a specific query parameter exists
$bool = $request->getQuery()->has('username'); // Returns boolean

// Get a specific query parameter
$username = $request->getQuery()->get('username');
```

### Trigger response and error handling
Phapi uses Exceptions to trigger responses to the client. This approach results in three different types of Exceptions: **Error**, **Success** and **Redirect**.

Phapi Exceptions accepts the following arguments:

* **$message** - More information given to the user (shown to the user)
* **$code** - Error code (shown to the user)
* **$previous** - Previous exception (used for logging)
* **$link** - A link to error documentation (shown to the user)
* **$logInformation** - Information that goes in the log. Useful for debugging. (used for logging)
* **$description** - An error message (shown to the user). All Phapi Exceptions have predefined error messages so you can pass ***null*** to use the predifined message.
* **$location** - Location used for redirects

#### Error
Lets use an Internal Server Error as example:

```php
throw new \Phapi\Exception\Error\InternalServerError(
    'We where unable to change the username due to an unknown error.'
    53,
    null,
    'http://docs.some.site/errors/53/'
    'The error occurred when the user tried to change username.'
    'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.'
);
```

This will result in that the exception handler will be cllaed and it will:
* Log the error to the configured logger (the **message**, **logInformation** will be included in the log as well as the file and line number, chained exceptions will also be included in the log).
* Create a response based on the arguments passed to the exception. Example:

  ```json
  {
    "errors":
      {
        "description": "An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.",
        "message": "We where unable to change the username due to an unknown error.",
        "code": 53,
        "link": "http://docs.some.site/errors/53/"
      }
  }
```

* The registered response handler will be triggered and the response will be sent to the client.

##### Extending exceptions
When an error occurs that doesn't match any of the other predefined Phapi\Exceptions an Internal Server Error will be thrown.

It's recommended to extend the InternalServerError Exception to get all of Phapi Exceptions functionality if new Exceptions are created.

#### Success
A 200 Ok response are automatically triggered when the request results in a valid response. The Phapi\Exception\Success\Ok Exception is thrown to trigger the registered response handler.

Valid responses are:
* Ok (note: should not be thrown manually, it will automatically be thrown by the application)
* Created
* Accepted
* NoContent
* NotModified

An example usage in a resource might be that a POST has been made and a new user should be created. When the user has been created the following code can be used to trigger a 201 Created response (no arguments are needed):
```php
throw new \Phapi\Exception\Created();
```

#### Redirect
Redirects are used when a resource has moved, permanently or temporarily.

Valid redirects are:
* MovedPermanently
* TemporaryRedirect

Take an example where the resource ***/user/peter*** has changed to ***/users/peter***. Then the following code should be included in the resource that previously handleded ***/user/peter***:

```php
throw new \Phapi\Exception\Redirect\MovedPermanently('/users/peter');
```

This will result in a 301 Moved Permanently response with the passed argument (***/users/peter***) assigned to the location header.

## License
Phapi is licensed under the MIT License - see the LICENSE file for details
