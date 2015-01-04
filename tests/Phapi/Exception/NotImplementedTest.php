<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\NotImplemented;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\NotImplemented
 */
class NotImplementedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 501;
    public $statusMessage = 'Not Implemented';
    public $link = null;
    public $code = null;
    public $description = 'The requested method is not implemented.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new NotImplemented();
        return $exception;
    }

    use exceptionTests;
}