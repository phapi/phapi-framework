<?php

namespace Phapi\Exception\Redirect;

use Phapi\Exception\Redirect;
use Phapi\Http\Response;

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
class MovedPermanently extends Redirect
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_MOVED_PERMANENTLY;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Moved Permanently';

}