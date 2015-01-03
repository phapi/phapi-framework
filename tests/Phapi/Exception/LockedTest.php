<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\Locked;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Locked
 */
class LockedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 423;
    public $statusMessage = 'Locked';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'The requested resource is currently locked.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new Locked();
        return $exception;
    }

    use exceptionTests;
}