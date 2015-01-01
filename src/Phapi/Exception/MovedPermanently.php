<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Moved Permanently
 *
 * Response code 301
 *
 * Moved Permanently
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class MovedPermanently extends Exception
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 301;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Moved Permanently';

    /**
     * Create exception
     *
     * @param null $redirect
     */
    public function __construct($redirect = null)
    {
        $this->location = $redirect;
    }
}