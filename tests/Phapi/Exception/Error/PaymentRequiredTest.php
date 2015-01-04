<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\PaymentRequired;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\PaymentRequired
 */
class PaymentRequiredTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 402;
    public $statusMessage = 'Payment Required';
    public $link = null;
    public $code = null;
    public $description = 'Payment is required before the requested method/resource can be requested.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new PaymentRequired();
        return $exception;
    }

    use exceptionTests;
}