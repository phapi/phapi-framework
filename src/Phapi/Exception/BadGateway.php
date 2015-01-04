<?php

namespace Phapi\Exception;

use Phapi\Exception;

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
class BadGateway extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 502;

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