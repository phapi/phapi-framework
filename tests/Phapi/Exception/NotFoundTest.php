<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\NotFound;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\NotFound
 */
class NotFoundTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 404;
    public $statusMessage = 'Not Found';
    public $link = null;
    public $code = null;
    public $description = 'The URI requested is invalid or the resource requested, such as a user, does not exists. Also returned when the requested format is not supported by the requested method.';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new NotFound();
        return $exception;
    }

    use exceptionTests;
}