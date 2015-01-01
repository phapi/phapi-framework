<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Temporary Redirect
 *
 * Response code 307
 *
 * Temporary redirect
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class TemporaryRedirect extends Exception
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 307;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Temporary Redirect';

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