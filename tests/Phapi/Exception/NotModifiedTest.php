<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\NotModified;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\NotModified
 */
class NotModifiedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 304;
    public $statusMessage = 'Not Modified';
    public $userInformationLink = null;
    public $code = null;
    public $message = null;
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new NotModified();
        return $exception;
    }

    use exceptionTests;
}