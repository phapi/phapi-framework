<?php

namespace Phapi\Exception\Success;

use Phapi\Exception\Success;
use Phapi\Http\Response;

/**
 * Class Accepted
 *
 * Response code 203
 *
 * Request accepted and set to be performed in a background task. Useful if your client is
 * requesting something on the API that is time-consuming and you don't want the client to have to wait.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Accepted extends Success {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_ACCEPTED;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Accepted';

}