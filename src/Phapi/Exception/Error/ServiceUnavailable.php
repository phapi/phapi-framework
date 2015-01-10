<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Service Unavailable
 *
 * Response code 504
 *
 * The API is up, but overloaded with requests. Try again later.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class ServiceUnavailable extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_SERVICE_UNAVAILABLE;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Service Unavailable';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The API is up, but overloaded with requests. Try again later.';
}
