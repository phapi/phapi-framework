<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\NotAcceptable;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\NotAcceptable
 */
class NotAcceptableTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 406;
    public $statusMessage = 'Not Acceptable';
    public $link = null;
    public $code = null;
    public $description = 'Returned by the API when an invalid format is specified in the request.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new NotAcceptable();
        return $exception;
    }

    use exceptionTests;
}