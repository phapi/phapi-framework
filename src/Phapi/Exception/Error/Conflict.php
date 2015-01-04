<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;

/**
 * Class Bad Gateway
 *
 * Response code 409
 *
 * The submitted data is causing a conflict with the current
 * state of the resource. An accompanying error message will explain why.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Conflict extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 409;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Conflict';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The submitted data is causing a conflict with the current state of the resource. An accompanying error message will explain why.';
}