<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\XML;

/**
 * @coversDefaultClass \Phapi\Serializer\XML
 */
class XMLTest extends \PHPUnit_Framework_TestCase {

    public $serializer;

    public function setUp()
    {
        $this->serializer = new XML();
    }
    /**
     * @covers ::supports
     */
    public function testSupports()
    {
        $this->assertFalse($this->serializer->supports('application/json'));
        $this->assertFalse($this->serializer->supports('text/html', true));

        $this->assertTrue($this->serializer->supports('application/xml'));
        $this->assertTrue($this->serializer->supports('application/xml', true));
    }

    /**
     * @covers ::getContentTypes
     */
    public function testGetContentTypes()
    {
        $this->assertEquals(['application/xml'], $this->serializer->getContentTypes());
        $this->assertEquals(['application/xml'], $this->serializer->getContentTypes(true));
    }

    /**
     * @covers ::serialize
     * @covers ::arrayToXML
     */
    public function testSerialize()
    {
        $array = [
            "users" => [
                "id" => 1,
                "username" => "phapi",
                "name" => "Phapi"
            ],
            "count" => 8,
            "test" => ["one","two"]
        ];

        $xml = '<?xml version="1.0"?>
<response><users><id>1</id><username>phapi</username><name>Phapi</name></users><count>8</count><test><item0>one</item0><item1>two</item1></test></response>
';

        $this->assertEquals($xml, $this->serializer->serialize($array));
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserialize()
    {
        $xml_string = "<breakfast_menu>
<food>
<name>Belgian Waffles</name>
<price>$5.95</price>
<description>Two of our famous Belgian Waffles with plenty of real maple syrup</description>
<calories>650</calories>
</food>
<food>
<name>Strawberry Belgian Waffles</name>
<price>$7.95</price>
<description>Light Belgian waffles covered with strawberries and whipped cream</description>
<calories>900</calories>
</food>
</breakfast_menu>";

        $array = [
            'food' => [
                [
                    'name' => 'Belgian Waffles',
                    'price' => '$5.95',
                    'description' => 'Two of our famous Belgian Waffles with plenty of real maple syrup',
                    'calories' => 650
                ],
                [
                    'name' => 'Strawberry Belgian Waffles',
                    'price' => '$7.95',
                    'description' => 'Light Belgian waffles covered with strawberries and whipped cream',
                    'calories' => 900
                ]
            ]
        ];
        $this->assertEquals($array, $this->serializer->deserialize($xml_string));
    }

    /**
     * @covers ::deserialize
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testDeserializeFail()
    {
        $xml_string = "<breakfast_menu>
<food>
<name>Belgian Waffles</name>
<price>$5.95</price>
<description>Two of our famous Belgian Waffles with plenty of real maple syrup</description>
<calories>650
</food>
<food>
<name>Strawberry Belgian Waffles</name>
<price>$7.95</price>
<description>Light Belgian waffles covered with strawberries and whipped cream</description>
<calories>900</calories>
</food>
</breakfast_menu>";

        $array = [
            'food' => [
                [
                    'name' => 'Belgian Waffles',
                    'price' => '$5.95',
                    'description' => 'Two of our famous Belgian Waffles with plenty of real maple syrup',
                    'calories' => 650
                ],
                [
                    'name' => 'Strawberry Belgian Waffles',
                    'price' => '$7.95',
                    'description' => 'Light Belgian waffles covered with strawberries and whipped cream',
                    'calories' => 900
                ]
            ]
        ];
        $this->assertEquals($array, $this->serializer->deserialize($xml_string));
    }

    /**
     * @covers ::deserialize
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testDeserializeFail2()
    {
        $xml_string = "<breakfast_menu></breakfast_menu>";

        $this->serializer->deserialize($xml_string);
    }

    /**
     * @covers ::serialize
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testSerializeFail()
    {
        //$serializer->serialize("\xB1\x31");
    }
}
 