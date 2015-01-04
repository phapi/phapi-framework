<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\RequestEntityTooLarge;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\RequestEntityTooLarge
 */
class RequestEntityTooLargeTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 413;
    public $statusMessage = 'Request Entity Too Large';
    public $link = null;
    public $code = null;
    public $description = 'The requested entity is too large.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new RequestEntityTooLarge();
        return $exception;
    }

    use exceptionTests;
}