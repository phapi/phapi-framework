<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Tests\Cache;

use Phapi\Cache\NullCache;

/**
 * @coversDefaultClass \Phapi\Cache\NullCache
 */
class NullCacheTest extends \PHPUnit_Framework_TestCase {

    public function testSet()
    {
        $cache = new NullCache();
        $cache->connect();
        $this->assertFalse($cache->set('key', 'value'));
    }

    public function testGet()
    {
        $cache = new NullCache();
        $this->assertFalse($cache->get('key'));
    }

    public function testHas()
    {
        $cache = new NullCache();
        // check if the key exists
        $this->assertFalse($cache->has('key'));
    }

    public function testClear()
    {
        $cache = new NullCache();
        // remove key from cache
        $this->assertTrue($cache->clear('key'));
    }

    public function testFlush()
    {
        $cache = new NullCache();
        // remove key from cache
        $this->assertTrue($cache->flush());
    }
}