<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\MethodNotAllowed;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\MethodNotAllowed
 */
class MethodNotAllowedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 405;
    public $statusMessage = 'Method Not Allowed';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'The requested method is not allowed.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new MethodNotAllowed();
        return $exception;
    }

    use exceptionTests;
}