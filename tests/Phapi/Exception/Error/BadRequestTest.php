<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\BadRequest;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\BadRequest
 */
class BadRequestTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 400;
    public $statusMessage = 'Bad Request';
    public $link = null;
    public $code = null;
    public $description = 'The request was invalid or cannot be otherwise served. An accompanying error message will explain further.';
    public $message = 'The request doesn\'t include the identifier header';
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new BadRequest($this->message);
        return $exception;
    }

    use exceptionTests;
}