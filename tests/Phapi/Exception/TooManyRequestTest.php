<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\TooManyRequests;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\TooManyRequests
 */
class TooManyRequestsTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 429;
    public $statusMessage = 'Too Many Requests';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'Returned when a request cannot be served due to the application\'s rate limit having been exhausted for the resource.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new TooManyRequests();
        return $exception;
    }

    use exceptionTests;
}