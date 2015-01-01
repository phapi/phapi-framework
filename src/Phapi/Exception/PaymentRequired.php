<?php

namespace Phapi\Exception;

use Phapi\Exception;

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
class PaymentRequired extends Exception {

    /**
     * Response status code
     *
     * @var int
     */
    protected $statusCode = 402;

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
    protected $errorMessage = 'Payment is required before the requested method/resource can be requested.';
}