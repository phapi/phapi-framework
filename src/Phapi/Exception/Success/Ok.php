<?php

namespace Phapi\Exception\Success;

use Phapi\Exception\Success;
use Phapi\Http\Response;

/**
 * Class Ok
 *
 * Response code 200
 *
 * Success!
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Ok extends Success {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_OK;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Ok';

}