<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Request Entity Too Large
 *
 * Response code 413
 *
 * The requested entity is too large.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class RequestEntityTooLarge extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_REQUEST_ENTITY_TOO_LARGE;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Request Entity Too Large';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The requested entity is too large.';
}