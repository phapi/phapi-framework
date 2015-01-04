<?php

namespace Phapi\Exception;

use Phapi\Exception;

/**
 * Class Locked
 *
 * Response code 423
 *
 * The requested resource is currently locked
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Locked extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 423;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Locked';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'The requested resource is currently locked.';
}