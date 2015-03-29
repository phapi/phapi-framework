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

    public function testDefaultCache()
    {
        $app = new Phapi();
        $this->assertInstanceOf('\Phapi\Cache\NullCache', $app['cache']);
    }

    public function testDefaultPipeline()
    {
        $app = new Phapi();
        $this->assertInstanceOf('\Phapi\Contract\Pipeline', $app['pipeline']);
    }

    public function testDefaultRequest()
    {
        $app = new Phapi();
        $this->assertInstanceOf('\Psr\Http\Message\ServerRequestInterface', $app['request']);
    }

    public function testDefaultResponse()
    {
        $app = new Phapi();
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $app['response']);
    }
}