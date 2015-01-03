<?php

namespace Phapi\Exception;

use Phapi\Exception;

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
class UnsupportedMediaType extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 415;

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
    protected $message = 'Media type not supported.';
}