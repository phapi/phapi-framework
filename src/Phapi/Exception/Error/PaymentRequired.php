<?php

namespace Phapi\Exception\Error;

use Phapi\Exception\Error;
use Phapi\Http\Response;

/**
 * Class Payment Required
 *
 * Response code 402
 *
 * Payment is required before the requested method/resource can be requested.
 *
 * @category Exception
 * @package  Phapi\Exception
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class PaymentRequired extends Error {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = Response::STATUS_PAYMENT_REQUIRED;

    /**
     * Response status message
     *
     * @var string
     */
    protected $statusMessage = 'Payment Required';

    /**
     * Error message
     *
     * @var string
     */
    protected $description = 'Payment is required before the requested method/resource can be requested.';
}