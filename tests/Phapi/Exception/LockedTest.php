<?php

namespace Phapi\Tests\Exception;

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
    public $link = null;
    public $code = null;
    public $description = 'The requested resource is currently locked.';
    public $message = null;
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