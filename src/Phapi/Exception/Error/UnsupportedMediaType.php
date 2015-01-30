<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Unsupported Media Type
 *
 * Response code 415
 *
 * Media type not supported.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class UnsupportedMediaType extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_UNSUPPORTED_MEDIA_TYPE;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Unsupported Media Type';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'Media type not supported.';
}