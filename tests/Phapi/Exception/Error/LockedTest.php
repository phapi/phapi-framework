<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\Locked;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\Locked
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
        return new Locked();
    }

    use exceptionTests;
}