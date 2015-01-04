<?php

namespace Phapi\Tests\Exception\Redirect;

use Phapi\Exception\Redirect\TemporaryRedirect;
use Phapi\Tests\Exception\exceptionTests;

require_once __DIR__ . '/../ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Redirect\TemporaryRedirect
 */
class TemporaryRedirectTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 307;
    public $statusMessage = 'Temporary Redirect';
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
        return new TemporaryRedirect($this->location);
    }

    use exceptionTests;
}