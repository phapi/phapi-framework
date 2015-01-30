<?php

namespace Phapi\Exception\Redirect;

use Phapi\Exception\Redirect;
use Phapi\Http\Response;

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
class TemporaryRedirect extends Redirect
{

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_TEMPORARY_REDIRECT;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Temporary Redirect';

}