<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Gone
 *
 * Response code 410
 *
 * This resource is gone. Used to indicate that an API endpoint has been turned off.
 * For example: "The REST API v1 will soon stop functioning. Please migrate to API v1.1."
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Gone extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 410;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Gone';

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = 'This resource is gone. Used to indicate that an API endpoint has been turned off. For example: "The REST API v1 will soon stop functioning. Please migrate to API v1.1."';
}