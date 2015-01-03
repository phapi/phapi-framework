<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\MovedPermanently;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\MovedPermanently
 */
class MovedPermanentlyTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 301;
    public $statusMessage = 'Moved Permanently';
    public $userInformationLink = null;
    public $code = null;
    public $message = null;
    public $userInformation = null;
    public $location = 'https://github.com/ahinko/phapi';
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new MovedPermanently($this->location);
        return $exception;
    }

    use exceptionTests;
}