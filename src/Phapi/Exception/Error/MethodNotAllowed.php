<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Method Not Allowed
 *
 * Response code 405
 *
 * The requested method is not allowed.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class MethodNotAllowed extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_METHOD_NOT_ALLOWED;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Method Not Allowed';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The requested method is not allowed.';
}