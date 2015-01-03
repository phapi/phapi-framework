<?php

namespace Phapi\Tests;

use Phapi\Bucket;

/**
 * @coversDefaultClass \Phapi\Bucket
 */
class BucketTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     */
    public function testConstructor()
    {
        return new Bucket([
            'name' => 'Phapi',
            'version' => 1,
            'underline' => 'yes'
        ]);
    }

    /**
     * @depends testConstructor
     * @covers ::get
     *
     * @param Bucket $bucket
     */
    public function testGet(Bucket $bucket)
    {
        $this->assertEquals(1, $bucket->get('version'));
        $this->assertEquals('Phapi', $bucket->get('name'));
    }

    /**
     * @depends testConstructor
     * @covers ::has
     * @covers ::get
     * @covers ::add
     *
     * @param Bucket $bucket
     */
    public function testAdd(Bucket $bucket)
    {
        $bucket->add([
            'version' => 2,
            'username' => 'donald'
        ]);
        $this->assertTrue($bucket->has('username'));
        $this->assertEquals(2, $bucket->get('version'));
    }

    /**
     * @depends testConstructor
     * @covers ::all
     *
     * @param Bucket $bucket
     */
    public function testAll(Bucket $bucket)
    {
        $this->assertEquals([
            'name' => 'Phapi',
            'version' => 2,
            'underline' => 'yes',
            'username' => 'donald'
        ], $bucket->all());
    }

    /**
     * @depends testConstructor
     * @covers ::keys
     *
     * @param Bucket $bucket
     */
    public function testKeys(Bucket $bucket)
    {
        $this->assertEquals(['name', 'version', 'underline', 'username'], $bucket->keys());
    }

    /**
     * @depends testConstructor
     * @covers ::replace
     *
     * @param Bucket $bucket
     */
    public function testReplace(Bucket $bucket)
    {
        $bucket->replace(['version' => 3]);
        $this->assertTrue($bucket->has('version'));
        $this->assertEquals(3, $bucket->get('version'));
    }

    /**
     * @depends testConstructor
     * @covers ::set
     * @covers ::has
     * @covers ::get
     *
     * @param Bucket $bucket
     */
    public function testSet(Bucket $bucket)
    {
        $bucket->set('username', 'Duck');
        $this->assertTrue($bucket->has('username'));
        $this->assertEquals('Duck', $bucket->get('username'));

        $bucket->set('color', 'black');
        $this->assertTrue($bucket->has('color'));
        $this->assertEquals('black', $bucket->get('color'));
    }

    /**
     * @depends testConstructor
     * @covers ::is
     *
     * @param Bucket $bucket
     */
    public function testIs(Bucket $bucket)
    {
        $bucket->set('aKey', 'aValue');
        $this->assertTrue($bucket->is('aKey', 'aValue'));

        // This will return false since the replace function has been tested earlier
        $this->assertFalse($bucket->is('underline', 'yes'));
    }

    /**
     * @depends testConstructor
     * @covers ::remove
     * @covers ::has
     * @covers ::get
     *
     * @param Bucket $bucket
     */
    public function testRemove(Bucket $bucket)
    {
        $bucket->remove('username');
        $this->assertFalse($bucket->has('username'));
        $this->assertEquals(null, $bucket->get('username'));

        $this->assertNotEquals('Duck', $bucket->get('username'));
    }

    /**
     * @depends testConstructor
     * @covers ::count
     *
     * @param Bucket $bucket
     */
    public function testCount(Bucket $bucket)
    {
        $this->assertEquals(3, $bucket->count());
    }
}
