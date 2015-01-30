<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\UnsupportedMediaType;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\UnsupportedMediaType
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
        return new UnsupportedMediaType();
    }

    use exceptionTests;
}