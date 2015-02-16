<?php
namespace Phapi\Tests\Cache;

use Phapi\Cache\Redis;

/**
 * @coversDefaultClass \Phapi\Cache\Redis
 */
class RedisTest extends \PHPUnit_Framework_TestCase {

    protected $cache;
    protected $key;
    protected $value;
    protected $replace;

    public function setUp()
    {
        $this->cache = new Redis([['host' => 'localhost', 'port' => 6379]]);
        $this->cache->connect();
        $this->key = 'test_'. time();
        $this->value = 'some test value';
        $this->replace = 'replaced test value';
    }

    /**
     * @covers ::__construct
     * @covers ::connect
     * @covers ::flush
     */
    public function testConstructor()
    {
        $this->cache = new Redis([['host' => 'localhost', 'port' => 6379]]);
        $this->assertTrue($this->cache->connect());
        $this->assertTrue($this->cache->flush());
    }

    /**
     * @covers ::__construct
     * @covers ::connect
     */
    public function testNotConnected()
    {
        $this->cache = new Redis([['host' => 'localhost', 'port' => 6376]]);
        $this->assertFalse($this->cache->connect());
    }

    /**
     * @depends testConstructor
     * @covers ::set
     * @covers ::get
     * @covers ::makeKey
     */
    public function testSetGet()
    {
        // set a key and a value
        $this->assertTrue($this->cache->set($this->key, $this->value));

        // get the value based on the key and validate it
        $this->assertEquals($this->value, $this->cache->get($this->key));

        $this->assertTrue($this->cache->set($this->key, $this->replace));
        $this->assertEquals($this->replace, $this->cache->get($this->key));
    }

    /**
     * @depends testConstructor
     * @covers ::has
     */
    public function testHas()
    {
        // set a key and a value
        $this->assertTrue($this->cache->set($this->key, $this->value));

        // check if the key exists
        $this->assertTrue($this->cache->has($this->key));
    }

    /**
     * @depends testConstructor
     * @covers ::clear
     */
    public function testClear()
    {
        // set a key and a value
        $this->assertTrue($this->cache->set($this->key, $this->value));

        // remove key from cache
        $this->assertTrue($this->cache->clear($this->key));

        // check if the key exists
        $this->assertEmpty($this->cache->get($this->key));
    }
}
