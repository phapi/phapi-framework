<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\Gone;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Gone
 */
class GoneTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 410;
    public $statusMessage = 'Gone';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'This resource is gone. Used to indicate that an API endpoint has been turned off. For example: "The REST API v1 will soon stop functioning. Please migrate to API v1.1."';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new Gone();
        return $exception;
    }

    use exceptionTests;
}