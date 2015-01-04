<?php

namespace Phapi\Tests\Exception\Success;

use Phapi\Exception\Success\NotModified;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Success\NotModified
 */
class NotModifiedTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 304;
    public $statusMessage = 'Not Modified';
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
        return new NotModified();
    }

    use exceptionTests;
}