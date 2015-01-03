<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\BadGateway;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\BadGateway
 */
class BadGatewayTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 502;
    public $statusMessage = 'Bad Gateway';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'The API is down or being upgraded.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new BadGateway();
        return $exception;
    }

    use exceptionTests;
}