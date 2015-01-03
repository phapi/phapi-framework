<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\NoContent;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\NoContent
 */
class NoContentTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 204;
    public $statusMessage = 'No Content';
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
        $exception = new NoContent();
        return $exception;
    }

    use exceptionTests;
}