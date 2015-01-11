<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Header;
use Phapi\Http\Response;

/**
 * @coversDefaultClass \Phapi\Http\Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     * @covers ::addHeaders
     * @covers ::getHeaders
     */
    public function testAddHeaders()
    {
        $response = new Response(new Header());
        $this->assertInstanceOf('Phapi\Http\Header', $response->getHeaders());
        $response->addHeaders(['X-Rate-Limit' => 800]);
        $this->assertEquals(800, $response->getHeaders()->get('X-Rate-Limit'));
    }

    /**
     * @covers ::__construct
     * @covers ::getHeaders
     */
    public function testGetHeaders()
    {
        $response = new Response(new Header(['X-Rate-Limit' => 600]));
        $this->assertInstanceOf('Phapi\Http\Header', $response->getHeaders());
        $this->assertEquals(600, $response->getHeaders()->get('X-Rate-Limit'));
    }

    /**
     * @covers ::setStatus
     * @covers ::getStatus
     */
    public function testStatus()
    {
        $response = new Response(new Header());
        $response->setStatus(500);
        $this->assertEquals(500, $response->getStatus());
    }

    /**
     * @covers ::setContentType
     * @covers ::getContentType
     */
    public function testContentType()
    {
        $response = new Response(new Header());
        $response->setContentType('application/json');
        $this->assertEquals('application/json', $response->getContentType());
    }

    /**
     * @covers ::getMessageForCode
     */
    public function testGetMessageForCode()
    {
        $response = new Response(new Header());
        $this->assertEquals('200 OK', $response->getMessageForCode(Response::STATUS_OK));
        $this->assertEquals('500 Internal Server Error', $response->getMessageForCode(Response::STATUS_INTERNAL_SERVER_ERROR));
        $this->assertEquals(null, $response->getMessageForCode(712));
    }

    /**
     * @covers ::setBody
     * @covers ::getBody
     * @covers ::clearBody
     * @covers ::addBody
     */
    public function testBody()
    {
        $response = new Response(new Header());
        $response->setBody(['username' => 'phapi']);
        $this->assertEquals(['username' => 'phapi'], $response->getBody());

        $response->addBody(['id' => 1234]);
        $this->assertEquals(['username' => 'phapi', 'id' => 1234], $response->getBody());

        $response->clearBody();
        $this->assertEquals([], $response->getBody());
    }

    /**
     * @covers ::__construct
     * @covers ::setLength
     * @covers ::getHeaders
     */
    public function testSetLength()
    {
        $response = new Response(new Header());
        $response->setLength(309);
        $this->assertEquals(309, $response->getHeaders()->get('Content-Length'));
    }

    /**
     * @covers ::setHttpVersion
     * @covers ::getHttpVersion
     */
    public function testHttpVersion()
    {
        $response = new Response(new Header());
        $response->setHttpVersion('1.0');
        $this->assertEquals('1.0', $response->getHttpVersion());
        $response->setHttpVersion('1.1');
        $this->assertEquals('1.1', $response->getHttpVersion());
    }

}