<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Internal Server Error
 *
 * Response code 500
 *
 * Something is broken.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class InternalServerError extends Error
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_INTERNAL_SERVER_ERROR;

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
    protected $description = 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix it as soon as possible.';

}