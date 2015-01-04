# Phapi
Phapi is a PHP based framework aiming at simplifying API development and a the same time being fast and small and not include functionality that others to better.

[![Build Status](https://travis-ci.org/ahinko/phapi.svg?branch=develop)](https://travis-ci.org/ahinko/phapi)
[![Code Climate](https://codeclimate.com/github/ahinko/phapi/badges/gpa.svg)](https://codeclimate.com/github/ahinko/phapi)
[![Test Coverage](https://codeclimate.com/github/ahinko/phapi/badges/coverage.svg)](https://codeclimate.com/github/ahinko/phapi)

**Please note that this project is far from finished and many of the core features are still missing.**

## Documentation
1. [Configuration](#configuration)
2. [Trigger response and error handling](#trigger-response-and-error-handling)

### Configuration
*TODO*

### Trigger response and error handling
Phapi uses Exceptions to trigger responses to the client. This approach results in three different types of Exceptions: **Errors**, **Responses** and **Redirects**.

Phapi Exceptions accepts the following arguments:

* **$message** - More information given to the user (shown to the user)
* **$code** - Error code (shown to the user)
* **$previous** - Previous exception (used for logging)
* **$link** - A link to error documentation (shown to the user)
* **$logInformation** - Information that goes in the log. Useful for debugging. (used for logging)
* **$description** - An error message (shown to the user). All Phapi Exceptions have predefined error messages so you can pass ***null*** to use the predifined message.
* **$location** - Location used for redirects

#### Errors
Lets use an Internal Server Error as example:

```
throw new \Phapi\Exception\InternalServerError(
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

  ```
  json:

  {
    "errors":
      {
        "description": "An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.",
        "message": "We where unable to change the username due to an unknown error.",
        "code": 53
        "link": "http://docs.some.site/errors/53/"
      }
  }
```

* The registered response handler will be triggered and the response will be sent to the client.

##### Extending exceptions
When an error occurs that doesn't match any of the other predefined Phapi\Exceptions an Internal Server Error will be thrown.

It's recommended to extend the InternalServerError Exception to get all of Phapi Exceptions functionality if new Exceptions are created.

#### Responses
A 200 Ok response are automatically triggered when the request results in a valid response. The Phapi\Exception\Ok Exception is thrown to trigger the registered response handler.

Valid responses are:
* Ok (note: should not be thrown manually, it will automatically be thrown by the application)
* Created
* Accepted
* NoContent
* NotModified

An example usage in a resource might be that a POST has been made and a new user should be created. When the user has been created the following code can be used to trigger a 201 Created response (no arguments are needed):
```
throw new \Phapi\Exception\Created();
```

#### Redirects
Redirects are used when a resource has moved, permanently or temporarily.

Valid redirects are:
* MovedPermanently
* TemporaryRedirect

Take an example where the resource ***/user/peter*** has changed to ***/users/peter***. Then the following code should be included in the resource that previously handleded ***/user/peter***:

```
throw new \Phapi\Exception\MovedPermanently('/users/peter');
```

This will result in a 301 Moved Permanently response with the passed argument (***/users/peter***) assigned to the location header.
