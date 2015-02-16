<?php

namespace Phapi\Tests;

use Phapi\RedisClient;

/**
 * @coversDefaultClass \Phapi\RedisClient
 */
class RedisClientTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     */
    public function testConstruct()
    {
        $client = new RedisClient();
        $this->assertInstanceOf('\Phapi\RedisClient', $client);
    }

    /**
     * @covers ::__construct
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testConstructFail()
    {
        $client = new RedisClient('localhost', 6376);
    }

    /**
     * @covers ::__call
     * @covers ::parseResponse
     */
    public function testCallSetGet()
    {
        $client = new RedisClient();
        $client->flushdb();
        $this->assertEquals('OK', $client->set('unitTest:aKey', 'a value'));
        $this->assertEquals('a value', $client->get('unitTest:aKey'));
        $this->assertEquals('1', $client->del('unitTest:aKey'));
        $this->assertEquals(null, $client->get('unitTest:aKey'));
    }

    /**
     * @covers ::__call
     * @covers ::parseResponse
     */
    public function testMultiResponse()
    {
        $client = new RedisClient();
        $this->assertEquals('1', $client->sadd('unitTest:aKey', 'value'));
        $this->assertEquals('1', $client->sadd('unitTest:aKey', 'another value'));
        $this->assertEquals('OK', $client->multi());
        $this->assertEquals('QUEUED', $client->smembers('unitTest:aKey'));
        $this->assertEquals('QUEUED', $client->smembers('unitTest:aKey'));
        $client->exec();
        $this->assertEquals(array('another value', 'value'), $client->smembers('unitTest:aKey'));
    }

    /**
     * @covers ::__call
     * @covers ::parseResponse
     * @expectedException \Exception
     */
    public function testResponseException()
    {
        $client = new RedisClient();
        $this->assertEquals('OK', $client->set('unitTest:aKey', 'the value'));
        $client->sadd('unitTest:aKey', 'another value', 'third value');
    }
}