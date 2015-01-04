<?php

namespace Phapi;

use Phapi\Exception\Accepted;
use Phapi\Exception\BadGateway;
use Phapi\Exception\BadRequest;
use Phapi\Exception\Conflict;
use Phapi\Exception\Created;
use Phapi\Exception\Forbidden;
use Phapi\Exception\Gone;
use Phapi\Exception\InternalServerError;
use Phapi\Exception\Locked;
use Phapi\Exception\MethodNotAllowed;
use Phapi\Exception\MovedPermanently;
use Phapi\Exception\NoContent;
use Phapi\Exception\NotAcceptable;
use Phapi\Exception\NotFound;
use Phapi\Exception\NotImplemented;
use Phapi\Exception\NotModified;
use Phapi\Exception\Ok;
use Phapi\Exception\PaymentRequired;
use Phapi\Exception\RequestEntityTooLarge;
use Phapi\Exception\RequestTimeout;
use Phapi\Exception\ServiceUnavailable;
use Phapi\Exception\TemporaryRedirect;
use Phapi\Exception\TooManyRequests;
use Phapi\Exception\Unauthorized;
use Phapi\Exception\UnprocessableEntity;
use Phapi\Exception\UnsupportedMediaType;
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
     * Storage of variables that middlewares
     * and the application might need.
     *
     * @var null|Bucket
     */
    public $registry = null;

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

        // Create a registry storage
        $this->registry = new Bucket([]);

        // Set up loggers
        $this->setLogWriter($this->configuration->get('logWriter'));

        // Check if we are in development mode
        if ($this->configuration->get('mode') === self::MODE_DEVELOPMENT) {
            // Show all errors
            error_reporting(E_ALL);
            // Display errors for easier development
            ini_set('display_errors', true);
        }
    }

    /**
     * Check if configuration and/or registry has key
     *
     * @param $key
     * @param int $storage
     * @return bool
     */
    public function has($key, $storage = self::STORAGE_BOTH)
    {
        // Check were to look
        if ($storage === self::STORAGE_CONFIGURATION) {
            // Only check in configuration
            return $this->configuration->has($key);
        } elseif ($storage === self::STORAGE_REGISTRY) {
            // Only check in registry
            return $this->registry->has($key);
        } else {
            // Check in both
            return ($this->registry->has($key) || $this->configuration->has($key)) ? true : false;
        }
    }

    /**
     * Get value from configuration and/or registry based on key
     *
     * @param $key
     * @param null $default
     * @param int $storage
     * @return bool|mixed|null
     */
    public function get($key, $default = null, $storage = self::STORAGE_BOTH)
    {
        // Check were to look
        if ($storage === self::STORAGE_CONFIGURATION) {
            // Only check in configuration
            return $this->configuration->get($key, $default);
        } elseif ($storage === self::STORAGE_REGISTRY) {
            // Only check in registry
            return $this->registry->get($key, $default);
        } else {
            // Check in both
            if ($value = $this->registry->get($key, $default) !== $default) {
                return $value;
            } else {
                return $this->configuration->get($key, $default);
            }
        }
    }

    /**
     * Check if configuration and/or registry has the value based on key
     *
     * @param $key
     * @param $value
     * @param int $storage
     * @return bool
     */
    public function is($key, $value, $storage = self::STORAGE_BOTH)
    {
        // Check were to look
        if ($storage === self::STORAGE_CONFIGURATION) {
            // Only check in configuration
            return $this->configuration->is($key, $value);
        } elseif ($storage === self::STORAGE_REGISTRY) {
            // Only check in registry
            return $this->registry->is($key, $value);
        } else {
            // Check in both
            return ($this->registry->is($key, $value) || $this->configuration->is($key, $value)) ? true : false;
        }
    }

    /**
     * Get the default configuration
     *
     * @return array
     */
    protected function getDefaultConfiguration()
    {
        return [
            'mode' => self::MODE_DEVELOPMENT
        ];
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
     * @throws \Exception
     */
    public function setLogWriter($logWriter = null)
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

            // try to write to the log
            $logWriter->log('The provided log writer might not be PSR-3 compatible since it does not implement the LoggerInterface.');

            // create an instance of the NullLogger instead
            $this->logWriter = new NullLogger();
        }

        // Remove logWriter from configuration
        $this->configuration->remove('logWriter');
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
        if ($this->isSuccessException($exception)) {
            // todo: set response status and leave the body as is

        } elseif ($this->isRedirectException($exception)) {
            // todo: set response status and redirect location
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
            if ($this->isErrorException($exception) === false) {
                // This is an uncaught exception that might not doesn't have the needed error information
                // so we need to handle it a little different than predefined exceptions
                // These exceptions will be handled as an Internal Server Error.
                $exception = new InternalServerError($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
            }
            // todo: set response status, and body (status code, status message, error code, information, link)
        }

        // todo: trigger response middleware(s)

        // If there was a previous nested exception call this function recursively to log that too.
        if ($prev = $exception->getPrevious()) {
            $this->exceptionHandler($prev);
        }
    }

    /**
     * Check if exception is a redirect exception
     *
     * @param \Exception $exception
     * @return bool
     */
    protected function isRedirectException(\Exception $exception)
    {
        if (
            $exception instanceof MovedPermanently ||
            $exception instanceof TemporaryRedirect
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check if exception is a success exception
     *
     * @param \Exception $exception
     * @return bool
     */
    protected function isSuccessException(\Exception $exception)
    {
        if (
            $exception instanceof Ok ||
            $exception instanceof Created ||
            $exception instanceof Accepted ||
            $exception instanceof NoContent ||
            $exception instanceof NotModified
        ) {
            return true;
        }
        return false;
    }


    /**
     * Check if exception is an Phapi Error Exception
     *
     * @param \Exception $exception
     * @return bool
     */
    protected function isErrorException(\Exception $exception)
    {
        if (
            $exception instanceof BadGateway ||
            $exception instanceof BadRequest ||
            $exception instanceof Conflict ||
            $exception instanceof Forbidden ||
            $exception instanceof Gone ||
            $exception instanceof Locked ||
            $exception instanceof MethodNotAllowed ||
            $exception instanceof NotAcceptable ||
            $exception instanceof NotFound ||
            $exception instanceof NotImplemented ||
            $exception instanceof PaymentRequired ||
            $exception instanceof RequestEntityTooLarge ||
            $exception instanceof RequestTimeout ||
            $exception instanceof ServiceUnavailable ||
            $exception instanceof TooManyRequests ||
            $exception instanceof Unauthorized ||
            $exception instanceof UnprocessableEntity ||
            $exception instanceof UnsupportedMediaType
        ) {
            return true;
        }
        return false;
    }
}