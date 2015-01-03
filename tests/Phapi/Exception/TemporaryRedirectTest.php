<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\TemporaryRedirect;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\TemporaryRedirect
 */
class TemporaryRedirectTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 307;
    public $statusMessage = 'Temporary Redirect';
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
        $exception = new TemporaryRedirect($this->location);
        return $exception;
    }

    use exceptionTests;
}