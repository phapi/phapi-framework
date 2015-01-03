<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\Conflict;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Conflict
 */
class ConflictTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 409;
    public $statusMessage = 'Conflict';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'The submitted data is causing a conflict with the current state of the resource. An accompanying error message will explain why.';
    public $userInformation = null;
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