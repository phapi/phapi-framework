<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Not Implemented
 *
 * Response code 501
 *
 * The requested method is not implemented.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class NotImplemented extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 501;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Not Implemented';

    /**
     * Error message
     *
     * @var string
     */
    protected $message = 'The requested method is not implemented.';
}