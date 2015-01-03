<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\BadRequest;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\BadRequest
 */
class BadRequestTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 400;
    public $statusMessage = 'Bad Request';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'The request was invalid or cannot be otherwise served. An accompanying error message will explain further.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new BadRequest();
        return $exception;
    }

    use exceptionTests;
}