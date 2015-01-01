<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\RequestEntityTooLarge;

/**
 * @coversDefaultClass \Phapi\Exception\RequestEntityTooLarge
 */
class RequestEntityTooLargeTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 413;
    public $statusMessage = 'Request Entity Too Large';
    public $link = null;
    public $errorCode = null;
    public $errorMessage = 'The requested entity is too large.';
    public $information = null;
    public $location = null;

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new RequestEntityTooLarge();
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