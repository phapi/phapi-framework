<?php

namespace Phapi\Tests\Exception\Success;

use Phapi\Exception\Success\Accepted;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Success\Accepted
 */
class AcceptedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 202;
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
        return new Accepted();
    }

    use exceptionTests;
}