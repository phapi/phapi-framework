<?php

namespace Phapi\Tests\Exception\Success;

use Phapi\Exception\Success\NoContent;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Success\NoContent
 */
class NoContentTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 204;
    public $statusMessage = 'No Content';
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
        $exception = new NoContent();
        return $exception;
    }

    use exceptionTests;
}