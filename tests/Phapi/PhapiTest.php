<?php

namespace Phapi\Tests;

use Phapi\Phapi;


/**
 * @coversDefaultClass \Phapi\Phapi
 */
class PhapiTest extends \PHPUnit_Framework_TestCase {

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

        $this->assertEquals(Phapi::MODE_PRODUCTION, $phapi->get('mode'));
        $this->assertEquals(Phapi::MODE_PRODUCTION, $phapi->get('mode', null, Phapi::STORAGE_CONFIGURATION));
        $this->assertEquals(null, $phapi->get('mode', null, Phapi::STORAGE_REGISTRY));
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
