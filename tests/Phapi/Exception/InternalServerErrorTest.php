<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\InternalServerError;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\InternalServerError
 */
class InternalServerErrorTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 500;
    public $statusMessage = 'Internal Server Error';
    public $userInformationLink = 'https://github.com/ahinko/phapi';
    public $code = 10;
    public $message = 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.';
    public $userInformation = 'An unexpected error occurred.';
    public $location = 'https://github.com/ahinko/phapi';
    public $logInformation = 'This will be logged!';

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new InternalServerError($this->message, $this->code, null, $this->logInformation, $this->userInformation, $this->userInformationLink, $this->location);
        return $exception;
    }

    use exceptionTests;
}