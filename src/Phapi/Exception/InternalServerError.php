<?php


namespace Phapi\Exception;

use Phapi\Exception;

class InternalServerError extends Exception
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 500;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Internal Server Error';

    /**
     * Information
     *
     * @var string
     */
    protected $information = 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.';

}