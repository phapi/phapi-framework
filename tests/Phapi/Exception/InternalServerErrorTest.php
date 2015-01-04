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
    public $link = 'https://github.com/ahinko/phapi';
    public $code = 10;
    public $description = 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.';
    public $message = 'An unexpected error occurred.';
    public $location = 'https://github.com/ahinko/phapi';
    public $logInformation = 'This will be logged!';

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new InternalServerError($this->message, $this->code, null, $this->link, $this->logInformation, $this->description, $this->location);
        return $exception;
    }

    use exceptionTests;
}