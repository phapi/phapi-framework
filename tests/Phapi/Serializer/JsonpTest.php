<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\Jsonp;

/**
 * @coversDefaultClass \Phapi\Serializer\Jsonp
 */
class JsonpTest extends \PHPUnit_Framework_TestCase {

    public $serializer;

    public function setUp()
    {
        $this->serializer = new Jsonp();
    }

    /**
     * @covers ::serialize
     */
    public function testSerializeNoCallback()
    {
        $this->assertEquals('{"key":"value","another key":"second value"}', $this->serializer->serialize([ 'key' => 'value', 'another key' => 'second value']));
    }

    /**
     * @covers ::serialize
     * @covers ::setCallback
     */
    public function testSerializeCallback()
    {
        $this->serializer->setCallback('callbackFunction');
        $this->assertEquals('callbackFunction({"key":"value","another key":"second value"})', $this->serializer->serialize([ 'key' => 'value', 'another key' => 'second value']));
    }

    /**
     * @covers ::serialize
     * @covers ::setCallback
     */
    public function testEncodeFaultyCallback()
    {
        $this->serializer->setCallback('callback/Function');
        $this->assertEquals('{"key":"value","another key":"second value"}', $this->serializer->serialize([ 'key' => 'value', 'another key' => 'second value']));
    }

    /**
     * @covers ::unserialize
     */
    public function testUnserialize()
    {
        $this->assertEquals([ 'key' => 'value', 'another key' => 'second value'], $this->serializer->unserialize('{"key":"value","another key":"second value"}'));
    }

}