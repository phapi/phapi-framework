<?php

namespace Phapi\Tests\Serializer;

use Phapi\Serializer\FileUpload;

/**
 * @coversDefaultClass \Phapi\Serializer\FileUpload
 */
class FileUploadTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::serialize
     */
    public function testSerialize()
    {
        $input = 'a simple text string';
        $serializer = new FileUpload();

        $this->assertEquals($input, $serializer->serialize($input));
    }

    /**
     * @covers ::deserialize
     */
    public function testDeserialize()
    {
        $input = 'a simple output file text string';
        $serializer = new FileUpload();

        $this->assertEquals($input, $serializer->deserialize($input));
    }
}
 