<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\FormUrlEncoded;

/**
 * @coversDefaultClass \Phapi\Serializer\FormUrlEncoded
 */
class FormUrlEncodedTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $array = ['key' => 'value', 'another key' => 'second value'];
        $serializer = new FormUrlEncoded();

        $this->assertEquals('key=value&another+key=second+value', $serializer->serialize($array));
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserialize()
    {
        $array = ['key' => 'value', 'another_key' => 'second value'];
        $serializer = new FormUrlEncoded();

        $this->assertEquals($array, $serializer->deserialize('key=value&another+key=second+value'));
    }
}
 