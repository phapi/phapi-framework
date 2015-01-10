<?php

namespace Phapi\Exception\Success;

use Phapi\Exception\Success;
use Phapi\Http\Response;

/**
 * Class No Content
 *
 * Response code 204
 *
 * The response does not include any content.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class NoContent extends Success {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_NO_CONTENT;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'No Content';

}