<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\Accepted;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Accepted
 */
class AcceptedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 203;
    public $statusMessage = 'Accepted';
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
        $exception = new Accepted();
        return $exception;
    }

    use exceptionTests;
}