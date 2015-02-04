<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\PHP;

/**
 * @coversDefaultClass \Phapi\Serializer\PHP
 */
class PHPTest extends \PHPUnit_Framework_TestCase {

    public $serializer;

    public function setup()
    {
        $this->serializer = new PHP();
    }

    /**
     * @covers ::supports
     */
    public function testSupports()
    {
        $this->assertTrue($this->serializer->supports('application/x-php'));
        $this->assertTrue($this->serializer->supports('text/x-php', true));

        $this->assertFalse($this->serializer->supports('application/xml'));
        $this->assertFalse($this->serializer->supports('application/xml', true));
    }

    /**
     * @covers ::getContentTypes
     */
    public function testGetContentTypes()
    {
        $this->assertEquals(['text/x-php', 'application/x-php'], $this->serializer->getContentTypes());
        $this->assertEquals(['text/x-php', 'application/x-php'], $this->serializer->getContentTypes(true));

        $this->assertNotEquals(['application/xml', 'text/html'], $this->serializer->getContentTypes());
        $this->assertNotEquals(['application/json', 'text/json'], $this->serializer->getContentTypes(true));
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $input = [
            "key" => "value",
            "array" => [
                "key" => "another value"
            ]
        ];
        $expected = 'a:2:{s:3:"key";s:5:"value";s:5:"array";a:1:{s:3:"key";s:13:"another value";}}';
        $this->assertEquals($expected, $this->serializer->serialize($input));
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserialize()
    {
        $input = 'a:2:{s:3:"key";s:5:"value";s:5:"array";a:1:{s:3:"key";s:13:"another value";}}';
        $expected = [
            "key" => "value",
            "array" => [
                "key" => "another value"
            ]
        ];
        $this->assertEquals($expected, $this->serializer->deserialize($input));
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
     * @covers ::deserialize
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testDeserializeFail2()
    {
        $this->serializer->deserialize('');
    }

    /**
     * @covers ::serialize
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testSerializeFail()
    {
        $input = function () {
            $var = 'tmp';
        };

        $this->serializer->serialize($input);
    }
}
 