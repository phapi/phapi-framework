<?php

namespace Phapi\Tests\Exception\Error;

use Phapi\Exception\Error\Gone;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Error\Gone
 */
class GoneTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 410;
    public $statusMessage = 'Gone';
    public $link = null;
    public $code = null;
    public $description = 'This resource is gone. Used to indicate that an API endpoint has been turned off. For example: "The REST API v1 will soon stop functioning. Please migrate to API v1.1."';
    public $message = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        return new Gone();
    }

    use exceptionTests;
}