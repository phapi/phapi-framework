<?php

namespace Phapi\Tests;

use Phapi\Exception\Accepted;

/**
 * @coversDefaultClass \Phapi\Exception\Accepted
 */
class AcceptedTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        $exception = new Accepted();
        return $exception;
    }

    /**
     * @depends testConstructor
     * @covers ::getLink
     *
     * @param Accepted $accepted
     */
    public function testGetLink(Accepted $accepted)
    {
        $this->assertEquals($accepted->getLink(), null);
    }

    /**
     * @depends testConstructor
     * @covers ::getStatusCode
     *
     * @param Accepted $accepted
     */
    public function testGetStatusCode(Accepted $accepted)
    {
        $this->assertEquals($accepted->getStatusCode(), 203);
    }

    /**
     * @depends testConstructor
     * @covers ::getStatusMessage
     *
     * @param Accepted $accepted
     */
    public function testGetStatusMessage(Accepted $accepted)
    {
        $this->assertEquals($accepted->getStatusMessage(), 'Accepted');
    }

    /**
     * @depends testConstructor
     * @covers ::getErrorCode
     *
     * @param Accepted $accepted
     */
    public function testGetErrorCode(Accepted $accepted)
    {
        $this->assertEquals($accepted->getErrorCode(), null);
    }

    /**
     * @depends testConstructor
     * @covers ::getInformation
     *
     * @param Accepted $accepted
     */
    public function testGetInformation(Accepted $accepted)
    {
        $this->assertEquals($accepted->getInformation(), null);
    }

    /**
     * @depends testConstructor
     * @covers ::getLocation
     *
     * @param Accepted $accepted
     */
    public function testGetLocation(Accepted $accepted)
    {
        $this->assertEquals($accepted->getLocation(), null);
    }

    /**
     * @depends testConstructor
     * @covers ::getErrorMessage
     *
     * @param Accepted $accepted
     */
    public function testGetErrorMessage(Accepted $accepted)
    {
        $this->assertEquals($accepted->getErrorMessage(), null);
    }
}
