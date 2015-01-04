<?php

namespace Phapi\Tests\Exception\Success;

use Phapi\Exception\Success\Created;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Success\Created
 */
class CreatedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 201;
    public $statusMessage = 'Created';
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
        return new Created();
    }

    use exceptionTests;
}