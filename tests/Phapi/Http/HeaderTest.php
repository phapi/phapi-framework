<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Header;
use Phapi\Http\Request;

/**
 * @coversDefaultClass \Phapi\Http\Header
 */
class HeaderTest extends \PHPUnit_Framework_TestCase
{

    public $headers;

    public function setUp()
    {
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'CONTENT_LENGTH' => '302',
            'HOST' => 'localhost.dev',
            'CONNECTION' => 'keep-alive',
            'CACHE_CONTROL' => 'max-age=0',
            'ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
            'DNT' => 1,
            'ACCEPT_ENCODING' => 'gzip, deflate, sdch',
            'ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,sv;q=0.6'
        ];
        $this->headers = new Header($headers);
    }

    /**
     * @covers ::__construct
     * @covers ::get
     */
    public function testConstructor()
    {
        $input = [
            'CONTENT_TYPE' => 'application/json',
            'CONTENT_LENGTH' => '302',
            'HOST' => 'localhost.dev',
            'CONNECTION' => 'keep-alive',
            'CACHE_CONTROL' => 'max-age=0',
            'ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
            'DNT' => 1,
            'ACCEPT_ENCODING' => 'gzip, deflate, sdch',
            'ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,sv;q=0.6'
        ];
        $headers = new Header($input);
        $this->assertEquals('text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8', $headers->get('accept'));
        $this->assertEquals(['text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'], $headers->get('accept', null, false));

        $this->assertEquals(null, $headers->get('user-accept'));
        $this->assertEquals(['empty'], $headers->get('user-accept', 'empty', false));
    }

    /**
     * @covers ::has
     */
    public function testHas()
    {
        $this->assertTrue($this->headers->has('accept'));
        $this->assertFalse($this->headers->has('user-accept'));
    }

    /**
     * @covers ::contains
     */
    public function testContains()
    {
        $this->assertTrue($this->headers->contains('accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'));
        $this->assertFalse($this->headers->contains('accept', 'error'));
    }

    /**
     * @covers ::all
     */
    public function testAll()
    {
        $expected = [
            'content-type' => ['application/json'],
            'content-length' => ['302'],
            'host' => ['localhost.dev'],
            'connection' => ['keep-alive'],
            'cache-control' => ['max-age=0'],
            'accept' => ['text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8'],
            'user-agent' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36'],
            'dnt' => [1],
            'accept-encoding' => ['gzip, deflate, sdch'],
            'accept-language' => ['en-US,en;q=0.8,sv;q=0.6']
        ];

        $this->assertEquals($expected, $this->headers->all());
    }

    /**
     * @covers ::keys
     */
    public function testKeys()
    {
        $expected = ['content-type', 'content-length', 'host', 'connection',
            'cache-control', 'accept', 'user-agent', 'dnt', 'accept-encoding', 'accept-language'];

        $this->assertEquals($expected, $this->headers->keys());
    }

    /**
     * @covers ::replace
     * @covers ::all
     */
    public function testReplace()
    {
        $this->headers->replace(['accept' => 'changed header']);
        $this->assertEquals(['accept' => ['changed header']], $this->headers->all());
    }

    /**
     * @covers ::set
     * @covers ::get
     */
    public function testSet()
    {
        $this->headers->set('x-rate-limit-remaining', 800);
        $this->assertEquals(800, $this->headers->get('x-rate-limit-remaining'));

        $this->headers->set('x-rate-limit-remaining', 1000, false);
        $this->assertEquals([800, 1000], $this->headers->get('x-rate-limit-remaining', null, false));
    }

    /**
     * @covers ::add
     * @covers ::contains
     */
    public function testAdd()
    {
        $this->headers->add(['rate-limit' => 100]);
        $this->assertTrue($this->headers->contains('rate-limit', 100));
    }

    /**
     * @covers ::remove
     * @covers ::contains
     * @covers ::hasCacheControlDirective
     */
    public function testRemove()
    {
        $this->headers->remove('accept');
        $this->assertFalse($this->headers->has('accept'));

        $this->assertTrue($this->headers->hasCacheControlDirective('max-age'));
        $this->headers->remove('cache-control');
        $this->assertFalse($this->headers->hasCacheControlDirective('max-age'));
    }

    /**
     * @covers ::addCacheControlDirective
     * @covers ::getCacheControlDirective
     */
    public function testAddCacheControlDirective()
    {
        $this->headers->addCacheControlDirective('no-cache', null);
        $this->assertEquals(null, $this->headers->getCacheControlDirective('no-cache'));
    }

    /**
     * @covers ::removeCacheControlDirective
     * @covers ::hasCacheControlDirective
     */
    public function testRemoveCacheControlDirective()
    {
        $this->headers->removeCacheControlDirective('max-age');
        $this->assertFalse($this->headers->hasCacheControlDirective('max-age'));
    }
}