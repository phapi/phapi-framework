<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\Forbidden;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\Forbidden
 */
class ForbiddenTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 403;
    public $statusMessage = 'Forbidden';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'The request is understood, but it has been refused or access is not allowed. An accompanying error message will explain why.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new Forbidden();
        return $exception;
    }

    use exceptionTests;
}