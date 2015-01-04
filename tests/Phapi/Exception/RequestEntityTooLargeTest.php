<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\RequestEntityTooLarge;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\RequestEntityTooLarge
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