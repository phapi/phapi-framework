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
     * @covers ::deserialize
     */
    public function testDeserialize()
    {
        $this->assertEquals([ 'key' => 'value', 'another key' => 'second value'], $this->serializer->deserialize('{"key":"value","another key":"second value"}'));
    }

    /**
     * @covers ::deserialize
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testDeserializeFail()
    {
        $this->serializer->deserialize('{"key":"value","anotherkey","value}');
    }

    /**
     * @covers ::serialize
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testSerializeFail()
    {
        $this->serializer->serialize("\xB1\x31");
    }
}