<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\MethodNotAllowed;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\MethodNotAllowed
 */
class MethodNotAllowedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 405;
    public $statusMessage = 'Method Not Allowed';
    public $link = null;
    public $code = null;
    public $description = 'The requested method is not allowed.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        return new MethodNotAllowed();
    }

    use exceptionTests;
}