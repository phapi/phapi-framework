<?php

namespace Phapi\Tests;

use Phapi\Exception;
use Phapi\Exception\MovedPermanently;

/**
 * @coversDefaultClass \Phapi\Exception\MovedPermanently
 */
class MovedPermanentlyTest extends \PHPUnit_Framework_TestCase
{

    public $statusCode = 301;
    public $statusMessage = 'Moved Permanently';
    public $link = null;
    public $errorCode = null;
    public $errorMessage = null;
    public $information = null;
    public $location = 'https://github.com/ahinko/phapi';

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new MovedPermanently($this->location);
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