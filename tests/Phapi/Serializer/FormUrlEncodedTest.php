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

        $this->assertEquals($array, $serializer->serialize($array));
    }

    /**
     * @covers ::unserialize
     */
    public function testUnserialize()
    {
        $array = ['key' => 'value', 'another key' => 'second value'];
        $serializer = new FormUrlEncoded();

        $this->assertEquals($array, $serializer->unserialize($array));
    }
}
 