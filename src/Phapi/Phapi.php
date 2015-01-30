<?php

namespace Phapi;

use Negotiation\FormatNegotiator;
use Phapi\Cache\NullCache;
use Phapi\Exception\Error;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Exception\Redirect;
use Phapi\Exception\Success;
use Phapi\Http\Header;
use Phapi\Http\Request;
use Phapi\Http\Response;
use Phapi\Serializer\FileUpload;
use Phapi\Serializer\FormUrlEncoded;
use Phapi\Serializer\Json;
use Phapi\Serializer\Jsonp;
use Phapi\Tool\UUID;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class Phapi
 *
 * The main application class
 *
 * @category Phapi
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Phapi {

    const MODE_DEVELOPMENT = 0;
    const MODE_STAGING = 1;
    const MODE_PRODUCTION = 2;

    /**
     * Configuration
     *
     * @var null|Bucket
     */
    public $configuration = null;

    /**
     * Log writer
     *
     * @var null
     */
    protected $logWriter = null;

    /**
     * Cache
     *
     * @var null
     */
    protected $cache = null;

    /**
     * The request
     *
     * @var Request
     */
    protected $request;

    /**
     * The response
     *
     * @var Response
     */
    protected $response;

    /**
     * Middlewares
     *
     * @var array
     */
    protected $middleware;

    /**
     * Router
     *
     * @var Router
     */
    protected $router;

    /**
     * Dispatcher
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Negotiator
     *
     * @var Negotiator
     */
    protected $negotiator;

    /**
     * Create application
     *
     * @param $configuration
     */
    public function __construct($configuration)
    {
        // Register exception handler
        set_exception_handler([$this, 'exceptionHandler']);
        // Register error handler
        set_error_handler([$this, 'errorHandler']);

        // Merge the default configuration with provided configuration and
        // create a new Bucket to save the configuration in.
        $this->configuration = new Bucket(array_merge($this->getDefaultConfiguration(), $configuration));

        // Set up loggers
        $this->setLogWriter($this->configuration->get('logWriter'));

        // Create the request object
        $this->request = new Request(
            $this->configuration->get('post'),
            $this->configuration->get('get'),
            $this->configuration->get('server'),
            $this->configuration->get('rawContent')
        );
        // Generate an UUID to use for both the request and response
        $this->request->setUuid((new UUID())->uuid4());

        // Create the response object
        $this->response = new Response(new Header());
        $this->response->setHttpVersion($this->configuration->get('httpVersion'));
        $this->response->addHeaders(['Request-ID' => $this->request->getUuid()]);
        $this->response->setRequestMethod($this->request->getMethod());
        $this->response->setCharset($this->configuration->get('charset'));

        // Handle format negotiation
        $this->negotiator = new Negotiator(new FormatNegotiator(), $this->configuration, $this->request, $this->response);
        $this->negotiator->negotiate();

        // Set up cache
        $this->setCache($this->configuration->get('cache'));

        // Create Router
        $this->router = new Router(new RouteParser());
        $this->router->setCache($this->cache);

        // Create dispatcher
        $this->dispatcher = new Dispatcher($this->router);

        // Deserialize incoming body if a body exists
        $this->deserializeBody();

        // Special treatment of the JsonP serializer
        $this->addCallbackToJsonPSerializer();

        // Define default middleware stack
        $this->middleware = [$this];
    }

    /**
     * Special treatment of the JsonP serializer
     */
    protected function addCallbackToJsonPSerializer()
    {
        // Check if accept header indicates the JsonP serializer
        if ($this->request->getAccept() === 'application/javascript') {
            // Find the JsonP serializer
            foreach ($this->configuration->get('serializers') as $serializer) {
                if ($serializer instanceof Jsonp) {
                    // Set callback to null
                    $callback = null;

                    // Look for callback param in query
                    if ($this->request->getQuery()->has('callback')) {
                        $callback = $this->request->getQuery()->get('callback');
                    } elseif ($this->request->getBody()->has('callback')) {
                        // Look for callback param in body
                        $callback = $this->request->getBody()->get('callback');
                    }

                    // Set callback if it isn't null
                    if ($callback !== null) {
                        $serializer->setCallback($callback);
                    }
                }
            }
        }
    }

    /**
     * Get negotiator
     *
     * @return Negotiator
     */
    public function getNegotiator()
    {
        return $this->negotiator;
    }

    /**
     * Deserialize the body based on content negotiation
     */
    protected function deserializeBody()
    {
        // Check if any raw content (body) can be found.
        if ($this->request->hasRawContent()) {
            // Get serializer, since content negotiation has already been done
            // we can take for granted that we have a serializer that can handle
            // the content type, otherwise an exception has already been thrown.
            $serializer = $this->getSerializer($this->request->getContentType());

            // Get the raw content and deserialize it before setting it as the
            // body on the request object.
            $this->request->setBody($serializer->deserialize($this->request->getRawContent()));

            // Exit function
            return;
        }

        // No raw content found, set body to an empty array
        $this->request->setBody([]);
    }

    /**
     * Get serializer based on content type
     *
     * @param $contentType
     * @param $serialize
     * @return null|Serializer
     */
    protected function getSerializer($contentType, $serialize = false)
    {
        foreach ($this->configuration->get('serializers') as $serializer) {
            if ($serializer->supports($contentType, $serialize)) {
                return $serializer;
            }
        }
        return null;
    }

    /**
     * Get the default configuration
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return [
            'mode' => self::MODE_DEVELOPMENT,
            'httpVersion' => '1.1',
            'defaultAccept' => 'application/json',
            'charset' => 'utf-8',
            'serializers' => [
                new Json(),
                new Jsonp(),
                new FormUrlEncoded(),
                new FileUpload()
            ],
            'post' => $_POST,
            'get' => $_GET,
            'server' => $_SERVER,
            'rawContent' => file_get_contents('php://input')
        ];
    }

    /**
     * Set cache
     *
     * If supplied cache isn't a valid cache NullCache will be created.
     * The NullCache simulates a cache but isn't caching anything. This
     * simplifies the development since we don't have to check if there
     * actually are a valid cache to use. We can just ask the Cache (even
     * if its a NullCache) and we will get a response.
     *
     * @param $cache
     */
    protected function setCache($cache)
    {
        // Check if its an actual cache
        if ($cache instanceof Cache) {
            $this->cache = $cache;
            if ($this->cache->connect()) {
                // We have a working cache so we are done here.
                return;
            } else {
                // Could not connect to the configured cache. Log a warning about it
                $this->getLogWriter()->warning('Could not connect to the configured cache.');
            }
        }

        // As a fallback we create a NullCache
        $this->cache = new NullCache();
    }

    /**
     * Get Cache
     *
     * @return null
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Get log writer
     *
     * @return mixed|Logger|null
     */
    public function getLogWriter()
    {
        return $this->logWriter;
    }

    /**
     * Set log writer
     *
     * @param null $logWriter
     */
    protected function setLogWriter($logWriter = null)
    {
        // check if log writer is provided
        if (is_null($logWriter)) {
            // if no writer is provider, create an instance of the NullLogger that wont log anything
            $this->logWriter = new NullLogger();
        } elseif ($logWriter instanceof LoggerInterface) {
            // check if provided log writer is an instance of the Psr-3 Logger interface
            $this->logWriter = $logWriter;
        } else {
            // the provided log writer isn't an instance of the Psr-3 logger interface so
            // we don't know if its compatible with the framework. Therefore we will:

            // create an instance of the NullLogger instead
            $this->logWriter = new NullLogger();
        }

        // Remove logWriter from configuration
        $this->configuration->remove('logWriter');
    }

    /**
     * Get the request
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get router
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Add middleware
     *
     * This method prepends new middleware to the application middleware stack.
     * The argument must be an instance that subclasses Middleware.
     *
     * @param Middleware $newMiddleware
     */
    public function addMiddleware(Middleware $newMiddleware)
    {
        $newMiddleware->setApplication($this);
        $newMiddleware->setNextMiddleware($this->middleware[0]);
        array_unshift($this->middleware, $newMiddleware);
    }

    /**
     * Run
     *
     * This method invokes the middleware stack, including the core Phapi application;
     * the result is an array of HTTP status, header, and body. These three items
     * are returned to the HTTP client.
     */
    public function run()
    {
        // match request to route
        $this->router->match($this->request->getUri(), $this->request->getMethod());

        // take router params and add them to the request object
        $this->request->addAttributes($this->router->getParams());

        // call the first middleware and start the chain
        $this->middleware[0]->call();
    }

    /**
     * Call
     *
     * Call function that applies hooks and asks the router
     * to dispatch the request. If no matching route and resource
     * could be found a not found error will be thrown.
     */
    public function call()
    {
        // Dispatch and get result
        $result = $this->dispatcher->dispatch($this);

        // Check if a status is included in the result
        if (isset($result['status'])) {
            // Set body
            $this->response->setBody($result['body']);

            // Create the appropriate response status
            if ($result['status'] == Response::STATUS_ACCEPTED) {
                // It's a accepted status
                $status = new Success\Accepted();
            } elseif ($result['status'] == Response::STATUS_CREATED) {
                // It's a created status
                $status = new Success\Created();
            } else {
                // It's a status that shouldn't be included so assume that an OK status
                // should be used. This indicates that the developer tries to set the
                // status in a way that isn't supported. Errors\*, Redirects\* and NoContent
                // and NotModified should be thrown directly in the resource method since
                // the resource body should not be returned.
                $status = new Success\Ok();
            }
        } else {
            // Only a body is returned (no status included in the result) so create
            // a OK response status and set the body
            $this->response->setBody($result);
            $status = new Success\Ok();
        }

        // Trigger response
        throw new $status;
    }

    /**
     * Set a custom error handler to make sure that errors are logged.
     * Allows any non-fatal errors to be logged.
     *
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @param array $errcontext
     * @throws InternalServerError
     */
    public function errorHandler($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $codes = array(
            256   => 'E_USER_ERROR',
            512   => 'E_USER_WARNING',
            1024  => 'E_USER_NOTICE',
            2048  => 'E_STRICT',
            4096  => 'E_RECOVERABLE_ERROR',
            8192  => 'E_DEPRECATED',
            16384 => 'E_USER_DEPRECATED',
            8     => 'E_NOTICE',
            2     => 'E_WARNING'
        );

        $message = 'Error of level ';

        if (array_key_exists($errno, $codes)) {
            $message .= $codes[$errno];
        } else {
            $message .= sprintf('Unknown error level, code of %d passed', $errno);
        }

        $message .= sprintf(
            '. Error message was "%s" in file %s at line %d.',
            $errstr,
            $errfile,
            $errline
        );

        // Log message
        $this->getLogWriter()->error($message, $errcontext);

        throw new InternalServerError();
    }

    /**
     * Custom exception handler.
     *
     * Exceptions are used to trigger the response no matter if an error occurred or if everything went 200 OK.
     *
     * Exceptions (response codes) should be handled differently depending on the response code.
     * The first set of codes are Redirects and should set the Location header. The second set of
     * codes are errors and should therefor change the response content to the exceptions error
     * information and a log entry should be created. Success codes should not log or modify the body.
     *
     * @param \Exception $exception
     */
    public function exceptionHandler(\Exception $exception)
    {
        if ($exception instanceof Success) {
            // Do nothing, must look for Success since "else" needs to pick up all other exception types
        } elseif ($exception instanceof Redirect) {
            // Set redirect location
            $this->response->setLocation($exception->getLocation());
        } else {
            // Create a log entry
            $this->logErrorException($exception);

            // Check if the Exception is a Phapi Exception
            if (!($exception instanceof Error)) {
                // This is an uncaught exception that might not doesn't have the needed error information
                // so we need to handle it a little different than predefined exceptions
                // These exceptions will be handled as an Internal Server Error.
                $exception = new InternalServerError(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getPrevious()
                );
            }
            // Set response status, and body (message, code, description, link)
            $this->response->setBody($this->prepareErrorBody($exception));
        }

        // Set status code
        $this->response->setStatus($exception->getStatusCode());
        // Get the serializer for the response content type
        $serializer = $this->getSerializer($this->response->getContentType(), true);
        // Give the serializer information about the content type we are using
        $serializer->setContentType($this->response->getContentType());
        // Get the body from the response, serialize it, give it back to the response
        $this->response->setSerializedBody($serializer->serialize($this->response->getBody()));
        // Tell response to respond
        $this->response->respond();

        // If there was a previous nested exception call this function recursively to log that too.
        if ($prev = $exception->getPrevious()) {
            $this->exceptionHandler($prev);
        }
    }

    /**
     * Create a log entry about the error exception
     *
     * @param $exception
     */
    protected function logErrorException($exception)
    {
        // Prepare log message
        $message = sprintf(
            'Uncaught exception of type %s thrown in file %s at line %s%s.',
            get_class($exception),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage() ? sprintf(' with message "%s"', $exception->getMessage()) : ''
        );

        // Log error
        $this->getLogWriter()->error($message, array(
            'Exception file'  => $exception->getFile(),
            'Exception line'  => $exception->getLine(),
            'Exception trace' => $exception->getTraceAsString()
        ));
    }

    /**
     * Takes an Error Exception and gets the available error information
     * and creates a body of it and returns the body.
     *
     * @param $exception
     * @return array
     */
    protected function prepareErrorBody(Error $exception)
    {
        // Prepare body
        $body = [ 'errors' => [] ];
        // Check if a message has been defined
        if (!empty($message = $exception->getMessage())) {
            $body['errors']['message'] = $message;
        }
        // Check if an error code has been defined
        if (!empty($code = $exception->getCode())) {
            $body['errors']['code'] = $code;
        }
        // Check if a description exists
        if (!empty($description = $exception->getDescription())) {
            $body['errors']['description'] = $description;
        }
        // Check if a link has been specified
        if (!empty($link = $exception->getLink())) {
            $body['errors']['link'] = $link;
        }

        return $body;
    }
}