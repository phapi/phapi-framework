<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\RequestTimeout;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\RequestTimeout
 */
class RequestTimeoutTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 408;
    public $statusMessage = 'Request Timeout';
    public $link = null;
    public $code = null;
    public $description = 'The request timed out.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new RequestTimeout();
        return $exception;
    }

    use exceptionTests;
}