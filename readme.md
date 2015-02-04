# Phapi

Phapi is a PHP based framework aiming at simplifying API development and a the same time being fast and small and not include functionality that others to better.

[![Author](https://img.shields.io/badge/author-%40ahinko-blue.svg?style=flat-square)](https://twitter.com/ahinko)
[![Source](https://img.shields.io/badge/source-ahinko/phapi-blue.svg?style=flat-square)](https://github.com/ahinko/phapi)
[![Release](https://img.shields.io/github/release/ahinko/phapi.svg?style=flat-square)](https://github.com/ahinko/phapi/releases)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/ahinko/phapi/blob/master/LICENSE)  
[![Build Status](https://img.shields.io/travis/ahinko/phapi.svg?style=flat-square)](https://travis-ci.org/ahinko/phapi)
[![HHVM](https://img.shields.io/hhvm/ahinko/phapi.svg?style=flat-square)](http://hhvm.h4cc.de/package/ahinko/phapi)
[![Code Climate](https://img.shields.io/codeclimate/github/ahinko/phapi.svg?style=flat-square)](https://codeclimate.com/github/ahinko/phapi)
[![Test Coverage](https://img.shields.io/codeclimate/coverage/github/ahinko/phapi.svg?style=flat-square)](https://codeclimate.com/github/ahinko/phapi)
[![Downloads](https://img.shields.io/packagist/dt/ahinko/phapi.svg?style=flat-square)](https://packagist.org/packages/ahinko/phapi)

## Quick start
See the [Phapi example repo](https://github.com/ahinko/phapi-example) for an example of how to get started really fast.

## Documentation
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Routes](#routes)
5. [Resources](#resources)
6. [Logging](#logging)
7. [Cache](#cache)
8. [Serializers](#serializers)
9. [Request](#request)
10. [Trigger response and error handling](#trigger-response-and-error-handling)
11. [Uploading files](#uploading-files)
12. [Retrieving files](#retrieving-files)

### Requirements
Phapi requires PHP 5.5 or above.

### Installation
Use composer by editing your composer.json:
```json
...
{
    "require": {
        "ahinko/phapi": "1.0.*"
    }
}
...
```
or add requirement from command line:
```
php composer.phar require ahinko/phapi:1.0.*
```

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

### Routes
Phapi will invoke the first route that matches the current HTTP request’s URI and method.

If Phapi does not find routes with URIs that match the HTTP request URI and method, it will automatically return a 404 Not Found response.

#### Defining routes
Routes can be added by passing an array (with the route as the key and the name of the resource as the value) to the **addRoutes** function. It's also possible to add single routes using the **addRoute($route, $resource)** function.

```php
// Create a list of routes
$routes = [
  '/users'                  => '\\Phapi\\Resource\\Users',
  '/users/{name:a}'         => '\\Phapi\\Resource\\User',
  '/articles/{id:[0-9]+}'   => '\\Phapi\\Resource\\Article',
  '/blog/{slug}/{title:c}?' => '\\Phapi\\Resource\\Blog\\Post',
];

// Add routes to the router
$phapi->getRouter()->addRoutes($routes);
```

By default a route pattern syntax is used where **{foo}** specified a placeholder with name **foo** and matching the string **[^/]+**. To adjust the pattern the placeholder matches, you can specify a custom pattern by writing **{bar:[0-9]+}**.

##### Regex Shortcuts
```
:i => :/d+                # numbers only
:a => :[a-zA-Z0-9]+       # alphanumeric
:c => :[a-zA-Z0-9+_-\.]+  # alnumnumeric and +-_. characters
:h => :[a-fA-F0-9]+       # hex
```

use in routes:
```php
$routes = [
  '/user/{id:i}'
  '/blog/{title:c}'
]
```

### Resources
Set up autoloading by editing *composer.json*. Example:
```json
{
  "autoload": {
    "psr-4": {
      "Phapi\\Resource\\": "app/resource",
    }
  }
}
```

Resources must extend **Phapi\Resource**. A resource implements http methods. For example, if a resource should be able to respond to a **GET request** it must implement a method named **get**:
```php
namespace Phapi\Resource;

use Phapi\Resource;

class User extends Resource {

  public function get()
  {
    return [
      'user' => [
          'id' => 1,
          'name' => 'John Doe',
          'username' => 'johndoe99'
        ]
    ];
  }
}

```

Each method returns an array with the data that should be returned to the client (see example above). The array will be serialized based on the clients accept header. Json example:
```json
{
  "user":
  {
    "id": 1,
    "name": "John Doe",
    "username": "johndoe99"
  }
}
```

It's possible to access the request and response objects by calling:

```php
// Get the request
$request = $this->getRequest();
// Get the response
$response = $this->getResponse();
```

#### POST, PUT, and PATCH with Created or Accepted responses
In the GET example above (when a 200 OK is returned to the client) an array with the response body is returned from the function. This is how Phapi expects to get the body for 200 OK responses.

There are however two other response codes that needs some special treatment: 201 Created and 202 Accepted. These responses should in most cases include a body. Created should usually include a copy of the newly created entity while Accepted SHOULD include an indication of the request's current status and either a pointer to a status monitor or some estimate of when the user can expect the request to be fulfilled.

To be able to do this the response from the function needs to be a little bit different:

```php
public function post()
{
  return [
    'body' => [
      ... // Copy of the created entity
    ]
    'status' => Phapi\Http\Response::STATUS_CREATED
  ];
}
```
The body and status needs to be wrapped in an array.

See the [Trigger response and error handling](#trigger-response-and-error-handling) section for more information about how to handle errors and redirects.


#### OPTIONS and API documentation
There is a predefined options method that will respond to OPTIONS requests. The method returns supported content types, allowed methods as well as API documentation (if documentation exists).

Documenting the API is done by using PHPDoc and using tags that starts with **@api**.

```php
/**
 * @apiDescription Get information about a user
 * @apiUrl /users/:id
 */
 public function get()
 ...
```

It's possible to do quite advanced documentation this way.

*It might be slightly unusual to return a body on an OPTIONS request but this makes the API self-describing. It makes it possible to browse, use and look at the documentation of the API by actually using the API itself.*

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
    'cache'         => new \Phapi\Cache\Memcache(
      [
          ['host' => 'localhost', 'port' => 11211]
      ]
    ),
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
    new \Phapi\Serializer\Json(['application/vnd.phapi+json'], ['text/html']),
    new \Phapi\Serializer\Jsonp(['application/vnd.phapi+javascript']),
    new \Phapi\Serializer\FormUrlEncoded(),
    new \Phapi\Serializer\FileUpload(),
    new \Phapi\Serializer\XML(),
    new \Phapi\Serializer\PHP(),
    new \Phapi\Serializer\Yaml(),
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

- **query**, usually populated by the global GET variable.
- **body**, usually populated by php://input.
- **server**, usually populated by the global SERVER variable.
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

NoContent and NotModified should be used as Error\* and Redirect\* should be used. Just throw the exception and Phapi will take care of the rest.

Created, Accepted on the other hand must be used in a different maner. An example usage in a resource might be that a POST has been made and a new user should be created. When the user has been created two things should be included in the response to the client, a 201 status code and a copy of the newly created entity.
```php
public function post()
{
  return [
    'body' => [
        ... // Copy of the created entity
      ]
    'status' => Phapi\Http\Response::STATUS_CREATED
  ];
}
```

See the section about [Resources](#resources) for more information and examples.

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

## Uploading files
The basic workflow for handling file uploads are as follows:

- The client does a POST request to a resource (in this example: **Resource\Avatar** and the user id is **johndoe**) including information about the file that the client wants to upload. Filename, mime type, file size etc. Example:  

```json

POST /avatar/johndoe
{
  "filename": "johndoe.jpg",
  "mime-type": "image/jpg",
  "filesize": 123455
}

```

- The resource saves the information and generates (and returns) a unique URL to a resource that accepts PUT requests. Example:

```json
{
  "putUrl": "/avatar/johndoe"
}

```

- The client does a PUT request to the unique URL with the file content. Content type header should be set to **application/octet-stream**.

```
PUT /avatar/johndoe

...file content...


```

- The resource handling the PUT request can receive the file content from the request body:

```php
public function put()
{
  $fileContent = $this->getRequest()->getBody();
}
```

## Retrieving files
If the client expects to get a .jpg file then the accept header should be set to **image/jpg**. Since resources needs to return arrays the file content should be returned as the only value in the array:
```php
public function get()
{
  return [$fileContent];
}
```

The FileUpload serializer handles the serialization of files (i.e. it just passes through the file content without modifying it). The FileUpload serializer supports **image/jpg**, **image/jpeg**, **image/gif** and **image/png** by default so if more content/mime types needs to be supported those types needs to be [configured](#serializers):
```php

$configuration = [
  ...
  'serializers' => [
      ...
      new \Phapi\Serializer\FileUpload([], [ 'video/wmv '])
  ]
];

```

## License
Phapi is licensed under the MIT License - see the LICENSE file for details

## Contribute
Contribution, bug fixes etc are [always welcome](https://github.com/ahinko/phapi/issues/new).
