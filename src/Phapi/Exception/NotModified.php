<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Not Modified
 *
 * Class representing a 304 response code
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class NotModified extends Exception
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 304;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Not Modified';

    /**
     * Create exception
     */
    public function __construct()
    {
    }
}