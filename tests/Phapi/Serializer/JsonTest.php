<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\Json;

/**
 * @coversDefaultClass \Phapi\Serializer\Json
 */
class JsonTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::setAccept
     * @covers ::setContentType
     * @return Json
     */
    public function testConstruct()
    {
        $serializer = new Json([], ['text/html']);
        $serializer->setAccept('application/json');
        $serializer->setContentType('application/json');
        return $serializer;
    }

    /**
     * @covers ::supports
     * @depends testConstruct
     *
     * @param $serializer
     */
    public function testSupports($serializer)
    {
        $this->assertTrue($serializer->supports('application/json'));
        $this->assertTrue($serializer->supports('text/html', true));

        $this->assertFalse($serializer->supports('application/xml'));
        $this->assertFalse($serializer->supports('application/xml', true));
    }

    /**
     * @covers ::getContentTypes
     * @depends testConstruct
     *
     * @param $serializer
     */
    public function testGetContentTypes($serializer)
    {
        $this->assertEquals(['application/json', 'text/json'], $serializer->getContentTypes());
        $this->assertEquals(['application/json', 'text/json', 'text/html'], $serializer->getContentTypes(true));

        $this->assertNotEquals(['application/xml', 'text/html'], $serializer->getContentTypes());
        $this->assertNotEquals(['application/json', 'text/json'], $serializer->getContentTypes(true));
    }

    /**
     * @covers ::serialize
     * @depends testConstruct
     *
     * @param $serializer
     */
    public function testSerialize($serializer)
    {
        $this->assertEquals('{"key":"value","another key":"second value"}', $serializer->serialize([ 'key' => 'value', 'another key' => 'second value']));
    }

    /**
     * @covers ::deserialize
     * @depends testConstruct
     *
     * @param $serializer
     */
    public function testDeserialize($serializer)
    {
        $this->assertEquals([ 'key' => 'value', 'another key' => 'second value'], $serializer->deserialize('{"key":"value","another key":"second value"}'));
    }

    /**
     * @covers ::deserialize
     * @depends testConstruct
     * @expectedException \Phapi\Exception\Error\BadRequest
     *
     * @param $serializer
     */
    public function testDeserializeFail($serializer)
    {
        $serializer->deserialize('{"key":"value","anotherkey","value}');
    }

    /**
     * @covers ::serialize
     * @depends testConstruct
     * @expectedException \Phapi\Exception\Error\InternalServerError
     *
     * @param $serializer
     */
    public function testSerializeFail($serializer)
    {
        $serializer->serialize("\xB1\x31");
    }
}
 