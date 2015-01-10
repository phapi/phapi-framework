<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Bad Gateway
 *
 * Response code 502
 *
 * The API is down or being upgraded
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class BadGateway extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_BAD_GATEWAY;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Bad Gateway';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The API is down or being upgraded.';
}