<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;
use Phapi\Exception\UnprocessableEntity;

require_once __DIR__ . '/ExceptionTraits.php';

/**
 * @coversDefaultClass \Phapi\Exception\UnprocessableEntity
 */
class UnprocessableEntityTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 422;
    public $statusMessage = 'Unprocessable Entity';
    public $userInformationLink = null;
    public $code = null;
    public $message = 'Returned when an uploaded file is unable to be processed.';
    public $userInformation = null;
    public $location = null;
    public $logInformation = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new UnprocessableEntity();
        return $exception;
    }

    use exceptionTests;
}