<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\BadGateway;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\BadGateway
 */
class BadGatewayTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 502;
    public $statusMessage = 'Bad Gateway';
    public $link = null;
    public $code = null;
    public $description = 'The API is down or being upgraded.';
    public $message = 'Upgrade in progress';
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new BadGateway($this->message);
        return $exception;
    }

    use exceptionTests;
}