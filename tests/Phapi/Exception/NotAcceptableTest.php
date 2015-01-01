<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\NotAcceptable;

/**
 * @coversDefaultClass \Phapi\Exception\NotAcceptable
 */
class NotAcceptableTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 406;
    public $statusMessage = 'Not Acceptable';
    public $link = null;
    public $errorCode = null;
    public $errorMessage = 'Returned by the API when an invalid format is specified in the request.';
    public $information = null;
    public $location = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new NotAcceptable();
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