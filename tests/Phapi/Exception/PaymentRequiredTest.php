<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\PaymentRequired;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\PaymentRequired
 */
class PaymentRequiredTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 402;
    public $statusMessage = 'Payment Required';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'Payment is required before the requested method/resource can be requested.';
    public $userInformation = null;
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