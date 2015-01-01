<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Internal Server Error
 *
 * Class representing a 500 response code
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
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
     * Error message
     *
     * @var string
     */
    protected $errorMessage = 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.';

}