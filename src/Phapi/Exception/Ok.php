<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Ok
 *
 * Class representing a 200 response code
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Ok extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Ok';

}