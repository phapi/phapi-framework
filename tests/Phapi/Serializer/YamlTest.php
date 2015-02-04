<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\Yaml;

/**
 * @coversDefaultClass \Phapi\Serializer\Yaml
 */
class YamlTest extends \PHPUnit_Framework_TestCase {

    public $serializer;

    public function setup()
    {
        $this->serializer = new Yaml();
    }

    /**
     * @covers ::supports
     */
    public function testSupports()
    {
        $this->assertTrue($this->serializer->supports('application/x-yaml'));
        $this->assertTrue($this->serializer->supports('text/yaml', true));

        $this->assertFalse($this->serializer->supports('application/xml'));
        $this->assertFalse($this->serializer->supports('application/xml', true));
    }

    /**
     * @covers ::getContentTypes
     */
    public function testGetContentTypes()
    {
        $this->assertEquals(['application/x-yaml', 'text/x-yaml', 'text/yaml'], $this->serializer->getContentTypes());
        $this->assertEquals(['application/x-yaml', 'text/x-yaml', 'text/yaml'], $this->serializer->getContentTypes(true));

        $this->assertNotEquals(['application/xml', 'text/html'], $this->serializer->getContentTypes());
        $this->assertNotEquals(['application/json', 'text/json'], $this->serializer->getContentTypes(true));
    }

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $input = [
            "id" => 12,
            "name" => [
                "firstName" => "Phapi",
                "lastName" => "Framework"
            ]
        ];
        $expected = "id: 12
name:
    firstName: Phapi
    lastName: Framework
";

        $this->assertEquals($expected, $this->serializer->serialize($input));
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserialize()
    {
        $input = "id: 12
name:
    firstName: Phapi
    lastName: Framework";
        $expected = [
            "id" => 12,
            "name" => [
                "firstName" => "Phapi",
                "lastName" => "Framework"
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
        $input = "id: 12
name:firstName: Phapi
    lastName: Framework";

        $this->serializer->deserialize($input);
    }

    /**
     * @covers ::serialize
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testSerializeFail()
    {
        $input = [
            "id" => 12,
            "name" => [
                "firstName" => new \stdClass(),
                "lastName" => "Framework"
            ]
        ];

        $this->serializer->serialize($input);
    }
}