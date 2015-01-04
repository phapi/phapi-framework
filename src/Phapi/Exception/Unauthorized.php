<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Unauthorized
 *
 * Response code 401
 *
 * Authentication credentials were missing or incorrect.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Unauthorized extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 401;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Unauthorized';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'Authentication credentials were missing or incorrect.';
}