<?php

namespace Phapi;

use Phapi\Cache\NullCache;
use Phapi\Exception\Error;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Exception\Redirect;
use Phapi\Exception\Success;
use Phapi\Http\Header;
use Phapi\Http\Request;
use Phapi\Http\Response;
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

    const STORAGE_CONFIGURATION = 0;
    const STORAGE_REGISTRY = 1;
    const STORAGE_BOTH = 2;

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
     * Create application
     *
     * @param $configuration
     */
    public function __construct($configuration)
    {
        // As a default we don't want to display error messages, unless we are in development mode (see bellow).
        ini_set('display_errors', false);
        // Register exception handler
        set_exception_handler([$this, 'exceptionHandler']);
        // Register error handler
        set_error_handler([$this, 'errorHandler']);

        // Merge the default configuration with provided configuration and
        // create a new Bucket to save the configuration in.
        $this->configuration = new Bucket(array_merge($this->getDefaultConfiguration(), $configuration));

        // Check if we are in development mode
        if ($this->configuration->get('mode') === self::MODE_DEVELOPMENT) {
            // Show all errors
            error_reporting(E_ALL);
            // Display errors for easier development
            ini_set('display_errors', true);
            // Turn opcache off if mode is development
            ini_set('opcache.enable', false);
        }

        // Set up loggers
        $this->setLogWriter($this->configuration->get('logWriter'));

        // Create the request object
        $this->request = new Request();
        $this->request->setUuid((new UUID())->v4());

        // Create the response object
        $this->response = new Response(new Header());
        $this->response->setHttpVersion($this->configuration->get('httpVersion'));
        $this->response->addHeaders(['Request-ID' => $this->request->getUuid()]);

        // Set up cache
        $this->setCache($this->configuration->get('cache'));

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
            'serializers' => [
                new Json(),
                new Jsonp(),
                new FormUrlEncoded(),
            ]
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
    public function setCache($cache)
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
        $message = 'Error of level ';

        switch ($errno) {
            case E_USER_ERROR:
                $message .= 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $message .= 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $message .= 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $message .= 'E_STRICT';
                break;
            case E_RECOVERABLE_ERROR:
                $message .= 'E_RECOVERABLE_ERROR';
                break;
            case E_DEPRECATED:
                $message .= 'E_DEPRECATED';
                break;
            case E_USER_DEPRECATED:
                $message .= 'E_USER_DEPRECATED';
                break;
            case E_NOTICE:
                $message .= 'E_NOTICE';
                break;
            case E_WARNING:
                $message .= 'E_WARNING';
                break;
            default:
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
     * Exceptions are used to trigger the response no matter if
     * an error occurred or if everything went 200 OK.
     *
     * @param \Exception $exception
     */
    public function exceptionHandler(\Exception $exception)
    {
        // Exceptions (response codes) should be handled differently depending on the
        // response code. The first set of codes should not modify the response content.
        // The second set of codes are errors and should therefor change the response
        // content to the exceptions error information and a log entry should be created.
        if ($exception instanceof Success) {
            // Set response status and leave the body as is
            $this->response->setStatus($exception->getStatusCode());

        } elseif ($exception instanceof Redirect) {
            // Set response status and redirect location
            $this->response->setStatus($exception->getStatusCode());
            $this->response->setLocation($exception->getLocation());

        } else {
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

            // Check if the Exception is a Phapi Exception
            if (!($exception instanceof Error)) {
                // This is an uncaught exception that might not doesn't have the needed error information
                // so we need to handle it a little different than predefined exceptions
                // These exceptions will be handled as an Internal Server Error.
                $exception = new InternalServerError($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
            }
            // Set response status, and body (message, code, description, link)
            $this->response->setStatus($exception->getStatusCode());
            $this->response->setBody($this->prepareErrorBody($exception));
        }

        // todo: trigger response

        // If there was a previous nested exception call this function recursively to log that too.
        if ($prev = $exception->getPrevious()) {
            $this->exceptionHandler($prev);
        }
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