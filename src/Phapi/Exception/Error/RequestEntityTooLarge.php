<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;

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
    protected $statusCode = 413;

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