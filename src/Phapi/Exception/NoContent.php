<?php

namespace Phapi\Exception;

use Phapi\Exception;

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
class NoContent extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 204;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'No Content';

}