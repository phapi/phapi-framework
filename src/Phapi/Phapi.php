<?php

namespace Phapi;

use Phapi\Exception\Accepted;
use Phapi\Exception\Created;
use Phapi\Exception\InternalServerError;
use Phapi\Exception\NoContent;
use Phapi\Exception\Ok;

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

    public function __construct()
    {
        // As a default we don't want to display error messages, unless we are in development mode (see bellow).
        ini_set('display_errors', false);

        // todo: Check if we are in development mode
        //if ($mode === self::MODE_DEVELOPMENT) {
            // Show all errors
            error_reporting(E_ALL);
            // Display errors for easier development
            ini_set('display_errors', true);
        //}

        // Register exception handler
        set_exception_handler([$this, 'exceptionHandler']);
        // Register error handler
        set_error_handler([$this, 'errorHandler']);
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

        // todo: logging

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
        // write to log
        $message = sprintf(
            'Uncaught exception of type %s thrown in file %s at line %s%s.',
            get_class($exception),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getMessage() ? sprintf(' with message "%s"', $exception->getMessage()) : ''
        );

        // todo: log error (remember request UUID)
        /* $this->getLogWriter()->error($message, array(
            'Exception file'  => $exception->getFile(),
            'Exception line'  => $exception->getLine(),
            'Exception trace' => $exception->getTraceAsString()
        )); */

        // Check if it is an \Exception but not an inherited \Exception.
        if (get_class($exception) === 'Exception') {
            // This is an uncaught exception that doesn't have the needed error information
            // so we need to handle it a little different than predefined exceptions
            // These exceptions will be handled as an Internal Server Error.
            $exception = new InternalServerError();
        }

        // Exceptions (response codes) should be handled differently depending on the
        // response code. The first set of codes should not modify the response content.
        // The second set of codes are errors and should therefor change the response
        // content to the exceptions error information.
        if (
            $exception instanceof Ok ||
            $exception instanceof Created ||
            $exception instanceof Accepted ||
            $exception instanceof NoContent
        ) {
            // todo: set response status, and body (status code, status message, error code, information, link)
        } else {
            // todo: set response status, and body (status code, status message, error code, information, link)
        }

        // todo: trigger response middleware(s)

        // If there was a previous nested exception call this function recursively to log that too.
        if ($prev = $exception->getPrevious()) {
            $this->exceptionHandler($prev);
        }
    }
}