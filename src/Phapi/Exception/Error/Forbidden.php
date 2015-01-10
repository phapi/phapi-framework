<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Forbidden
 *
 * Response code 403
 *
 * The API is down or being upgraded
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Forbidden extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_FORBIDDEN;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Forbidden';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The request is understood, but it has been refused or access is not allowed. An accompanying error message will explain why.';
}