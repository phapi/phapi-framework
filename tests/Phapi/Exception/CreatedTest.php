<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\Created;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Created
 */
class CreatedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 201;
    public $statusMessage = 'Created';
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
        $exception = new Created();
        return $exception;
    }

    use exceptionTests;
}