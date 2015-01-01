<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\InternalServerError;

/**
 * @coversDefaultClass \Phapi\Exception\InternalServerError
 */
class InternalServerErrorTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 500;
    public $statusMessage = 'Internal Server Error';
    public $link = 'https://github.com/ahinko/phapi';
    public $errorCode = 10;
    public $errorMessage = 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix the problem as soon as possible.';
    public $information = 'An unexpected error occurred.';
    public $location = 'https://github.com/ahinko/phapi';

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new InternalServerError($this->errorCode, $this->errorMessage, $this->information, $this->link, $this->location);
        return $exception;
    }

    /**
     * @depends testConstructor
     * @covers ::getLink
     *
     * @param Exception $exception
     */
    public function testGetLink(Exception $exception)
    {
        $this->assertEquals($this->link, $exception->getLink());
    }

    /**
     * @depends testConstructor
     * @covers ::getStatusCode
     *
     * @param Exception $exception
     */
    public function testGetStatusCode(Exception $exception)
    {
        $this->assertEquals($this->statusCode, $exception->getStatusCode());
    }

    /**
     * @depends testConstructor
     * @covers ::getStatusMessage
     *
     * @param Exception $exception
     */
    public function testGetStatusMessage(Exception $exception)
    {
        $this->assertEquals($this->statusMessage, $exception->getStatusMessage());
    }

    /**
     * @depends testConstructor
     * @covers ::getErrorCode
     *
     * @param Exception $exception
     */
    public function testGetErrorCode(Exception $exception)
    {
        $this->assertEquals($this->errorCode, $exception->getErrorCode());
    }

    /**
     * @depends testConstructor
     * @covers ::getInformation
     *
     * @param Exception $exception
     */
    public function testGetInformation(Exception $exception)
    {
        $this->assertEquals($this->information, $exception->getInformation());
    }

    /**
     * @depends testConstructor
     * @covers ::getLocation
     *
     * @param Exception $exception
     */
    public function testGetLocation(Exception $exception)
    {
        $this->assertEquals($this->location, $exception->getLocation());
    }

    /**
     * @depends testConstructor
     * @covers ::getErrorMessage
     *
     * @param Exception $exception
     */
    public function testGetErrorMessage(Exception $exception)
    {
        $this->assertEquals($this->errorMessage, $exception->getErrorMessage());
    }
}