<?php

namespace Phapi\Tests\Exception\Success;

use Phapi\Exception\Success\Ok;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Success\Ok
 */
class OkTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 200;
    public $statusMessage = 'Ok';
    public $link = null;
    public $code = null;
    public $message = null;
    public $description = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        return new Ok();
    }

    use exceptionTests;
}