<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\Ok;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Ok
 */
class OkTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 200;
    public $statusMessage = 'Ok';
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
        $exception = new Ok();
        return $exception;
    }

    use exceptionTests;
}