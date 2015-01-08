<?php
namespace Phapi\Tests\Tool;

use Phapi\Tool\UUID;

/**
 * @coversDefaultClass \Phapi\Tool\UUID
 */
class UUIDTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::isValid
     */
    public function testGetHeaders()
    {
        $uuid = new UUID();
        $this->assertTrue($uuid->isValid('65b2da62-1640-4a11-a1a3-2c8e87afafe9')); //v4
    }

    /**
     * @covers ::generate
     * @covers ::v4
     */
    public function testGenerateV4()
    {
        $tool = new UUID();
        $uuid = $tool->generate();
        $this->assertTrue($tool->isValid($uuid)); //v4
    }

    /**
     * @covers ::generate
     * @covers ::v5
     */
    public function testGenerateV5()
    {
        $tool = new UUID();
        $uuid = $tool->generate('65b2da62-1640-4a11-a1a3-2c8e87afafe9', 'someText');
        $this->assertTrue($tool->isValid($uuid));
    }

    /**
     * @covers ::generate
     * @covers ::v5
     */
    public function testGenerateV5Fail()
    {
        $tool = new UUID();
        $this->assertFalse($tool->generate('65b2da62-1640-4a11-a-2c8e87afafe9', 'someText'));
    }
}