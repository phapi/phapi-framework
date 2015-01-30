<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Unprocessable Entity
 *
 * Response code 422
 *
 * Returned when an uploaded file is unable to be processed.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class UnprocessableEntity extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_UNPROCESSABLE_ENTITY;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Unprocessable Entity';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'Returned when an uploaded file is unable to be processed.';
}