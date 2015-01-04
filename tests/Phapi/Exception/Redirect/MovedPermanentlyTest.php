<?php

namespace Phapi\Tests\Exception\Redirect;

use Phapi\Exception\Redirect\MovedPermanently;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Redirect\MovedPermanently
 */
class MovedPermanentlyTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 301;
    public $statusMessage = 'Moved Permanently';
    public $link = null;
    public $code = null;
    public $message = null;
    public $description = null;
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