<?php
namespace Phapi\Tests;

use Phapi\Phapi;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Phapi
 */
class PhapiTest extends TestCase
{

    public function testDefaultConfiguration()
    {
        $app = new Phapi();
        $this->assertEquals(0, $app['mode']);
        $this->assertEquals('1.1', $app['httpVersion']);
        $this->assertEquals('application/json', $app['defaultAccept']);
        $this->assertEquals('utf-8', $app['charset']);
    }

    public function testDefaultLogger()
    {
        $app = new Phapi();
        $this->assertInstanceOf('\Psr\Log\NullLogger', $app['log']);
    }
}