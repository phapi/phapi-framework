<?php

namespace Phapi\Tests;

use Phapi\Phapi;
use Psr\Log\NullLogger;


/**
 * @coversDefaultClass \Phapi\Phapi
 */
class PhapiTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testConstruct()
    {
        $phapi = new Phapi([
            'httpVersion' => '1.1',
            'mode' => Phapi::MODE_DEVELOPMENT
        ]);
        $this->assertEquals('1.1', $phapi->get('httpVersion', null));
    }

    /**
     * @covers ::setLogWriter
     * @covers ::getLogWriter
     */
    public function testSetLogWriter()
    {
        $phapi = new Phapi([]);
        $phapi->setLogWriter(null);
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());

        $phapi->setLogWriter(new \stdClass());
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());

        $phapi->setLogWriter(new NullLogger());
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $phapi = new Phapi([
            'mode' => Phapi::MODE_PRODUCTION
        ]);

        $this->assertTrue($phapi->has('mode'));
        $this->assertTrue($phapi->has('mode', Phapi::STORAGE_CONFIGURATION));
        $this->assertFalse($phapi->has('mode', Phapi::STORAGE_REGISTRY));
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $phapi = new Phapi([
            'mode' => Phapi::MODE_PRODUCTION
        ]);

        $this->assertEquals(Phapi::MODE_PRODUCTION, $phapi->get('mode', null, Phapi::STORAGE_CONFIGURATION));
        $this->assertEquals(null, $phapi->get('mode', null, Phapi::STORAGE_REGISTRY));
        $this->assertEquals(Phapi::MODE_PRODUCTION, $phapi->get('mode'));

        $phapi->registry->add([
            'mode' => Phapi::MODE_DEVELOPMENT
        ]);

        $this->assertEquals(Phapi::MODE_DEVELOPMENT, $phapi->get('mode', null, Phapi::STORAGE_REGISTRY));

        $this->assertEquals(Phapi::MODE_DEVELOPMENT, $phapi->get('mode'));
    }

    /**
     * @covers ::is
     */
    public function testIs()
    {
        $phapi = new Phapi([
            'mode' => Phapi::MODE_PRODUCTION
        ]);

        $this->assertTrue($phapi->is('mode', Phapi::MODE_PRODUCTION));
        $this->assertTrue($phapi->is('mode', Phapi::MODE_PRODUCTION, Phapi::STORAGE_CONFIGURATION));
        $this->assertFalse($phapi->is('mode', Phapi::MODE_PRODUCTION, Phapi::STORAGE_REGISTRY));
    }
}
