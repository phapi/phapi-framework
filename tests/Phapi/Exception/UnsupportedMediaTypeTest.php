<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\UnsupportedMediaType;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\UnsupportedMediaType
 */
class UnsupportedMediaTypeTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 415;
    public $statusMessage = 'Unsupported Media Type';
    public $link = null;
    public $code = null;
    public $description = 'Media type not supported.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new UnsupportedMediaType();
        return $exception;
    }

    use exceptionTests;
}