<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Header;
use Phapi\Http\Response;

/**
 * @coversDefaultClass \Phapi\Http\Response
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {

    }

    public function testAddHeaders()
    {
    }

    public function testSetStatus()
    {
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

    public function testSetLength()
    {
    }

    public function testSetHttpVersion()
    {

    }

}