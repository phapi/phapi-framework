<?php

namespace Phapi\Exception\Success;

use Phapi\Exception\Success;
use Phapi\Http\Response;

/**
 * Class Not Modified
 *
 * Response code 304
 *
 * There was no new data to return
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class NotModified extends Success
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_NOT_MODIFIED;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Not Modified';

}