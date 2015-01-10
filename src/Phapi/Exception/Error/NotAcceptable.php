<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Not Acceptable
 *
 * Response code 406
 *
 * Returned by the API when an invalid format is specified in the request.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class NotAcceptable extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_NOT_ACCEPTABLE;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Not Acceptable';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'Returned by the API when an invalid format is specified in the request.';
}