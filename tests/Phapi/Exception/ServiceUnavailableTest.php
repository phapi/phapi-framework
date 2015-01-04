<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\ServiceUnavailable;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\ServiceUnavailable
 */
class ServiceUnavailableTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 504;
    public $statusMessage = 'Service Unavailable';
    public $link = null;
    public $code = null;
    public $description = 'The API is up, but overloaded with requests. Try again later.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new ServiceUnavailable();
        return $exception;
    }

    use exceptionTests;
}