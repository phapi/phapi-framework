<?php

namespace Phapi\Exception;

use Phapi\Exception;

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
class Forbidden extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 403;

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
    protected $message = 'The request is understood, but it has been refused or access is not allowed. An accompanying error message will explain why.';
}