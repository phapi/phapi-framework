<?php

namespace Phapi\Tests\Exception;

use Phapi\Exception;

trait exceptionTests
{
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
     * @covers ::getCode
     *
     * @param Exception $exception
     */
    public function testGetCode(Exception $exception)
    {
        $this->assertEquals($this->code, $exception->getCode());
    }

    /**
     * @depends testConstructor
     * @covers ::getDescription
     *
     * @param Exception $exception
     */
    public function testGetDescription(Exception $exception)
    {
        $this->assertEquals($this->description, $exception->getDescription());
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
     * @covers ::getMessage
     *
     * @param Exception $exception
     */
    public function testGetMessage(Exception $exception)
    {
        $this->assertEquals($this->message, $exception->getMessage());
    }

    /**
     * @depends testConstructor
     * @covers ::getLogInformation
     *
     * @param Exception $exception
     */
    public function testGetLogInformation(Exception $exception)
    {
        $this->assertEquals($this->logInformation, $exception->getLogInformation());
    }
}