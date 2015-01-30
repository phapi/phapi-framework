<?php
namespace Phapi\Tests\Cache;

use Phapi\Cache\NullCache;

/**
 * @coversDefaultClass \Phapi\Cache\NullCache
 */
class NullCacheTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::set
     * @covers ::connect
     */
    public function testSet()
    {
        $cache = new NullCache();
        $cache->connect();
        $this->assertFalse($cache->set('key', 'value'));
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $cache = new NullCache();
        $this->assertFalse($cache->get('key'));
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $cache = new NullCache();
        // check if the key exists
        $this->assertFalse($cache->has('key'));
    }

    /**
     * @covers ::clear
     */
    public function testClear()
    {
        $cache = new NullCache();
        // remove key from cache
        $this->assertTrue($cache->clear('key'));
    }

    /**
     * @covers ::flush
     */
    public function testFlush()
    {
        $cache = new NullCache();
        // remove key from cache
        $this->assertTrue($cache->flush());
    }
}