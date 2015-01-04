<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\Conflict;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\Conflict
 */
class ConflictTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 409;
    public $statusMessage = 'Conflict';
    public $link = null;
    public $code = null;
    public $description = 'The submitted data is causing a conflict with the current state of the resource. An accompanying error message will explain why.';
    public $message = null;
    public $location = null;
    public $logInformation = null;
    
    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new Conflict();
        return $exception;
    }

    use exceptionTests;
}