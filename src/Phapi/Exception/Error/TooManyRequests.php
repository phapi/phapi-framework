<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Too Many Requests
 *
 * Response code 429
 *
 * Returned when a request cannot be served due to the application's
 * rate limit having been exhausted for the resource.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class TooManyRequests extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_TOO_MANY_REQUESTS;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Too Many Requests';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'Returned when a request cannot be served due to the application\'s rate limit having been exhausted for the resource.';
}