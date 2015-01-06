<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Request;

/**
 * @coversDefaultClass \Phapi\Http\Request
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

    public $request;

    public function setUp()
    {
        $server = [
            'USER' => 'www-data',
            'HOME' => '/var/www',
            'FCGI_ROLE' => 'RESPONDER',
            'SCRIPT_FILENAME' => '/www/app/public_html/index.php',
            'QUERY_STRING' => 'test=test',
            'REQUEST_METHOD' => 'GET',
            'CONTENT_TYPE' => '',
            'CONTENT_LENGTH' => '',
            'SCRIPT_NAME' => '/index.php',
            'PATH_INFO' => '',
            'PATH_TRANSLATED' => '/www/app/public_html',
            'REQUEST_URI' => '/?test=test',
            'DOCUMENT_URI' => '/index.php',
            'DOCUMENT_ROOT' => '/www/app/public_html',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_SOFTWARE' => 'nginx/1.6.2',
            'REMOTE_ADDR' => '192.168.1.1',
            'REMOTE_PORT' => '50993',
            'SERVER_ADDR' => '192.168.1.10',
            'SERVER_PORT' => '80',
            'SERVER_NAME' => 'localhost',
            'HTTPS' => '',
            'REDIRECT_STATUS' => '200',
            'HTTP_HOST' => 'localhost',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_CACHE_CONTROL' => 'max-age=0',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36',
            'HTTP_DNT' => '1',
            'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,sv;q=0.6',
            'PHP_SELF' => '/index.php',
            'REQUEST_TIME_FLOAT' => '1420561694.801',
            'REQUEST_TIME' => '1420561694'
        ];
        $this->request = new Request([], [], $server, '');
    }

    /**
     * @covers ::setUuid
     * @covers ::getUuid
     */
    public function testUuid()
    {
        $this->request->setUuid('65b2da62-1640-4a11-a1a3-2c8e87afafe9');
        $this->assertEquals('65b2da62-1640-4a11-a1a3-2c8e87afafe9', $this->request->getUuid());
    }

    /**
     * @covers ::getMethod
     */
    public function testGetMethod()
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    /**
     * @covers ::isMethod
     */
    public function testIsMethod()
    {
        $this->assertTrue($this->request->isMethod('GET'));
        $this->assertFalse($this->request->isMethod('PUT'));
    }

    /**
     * @covers ::isGet
     * @covers ::isPost
     * @covers ::isCopy
     * @covers ::isPut
     * @covers ::isDelete
     * @covers ::isHead
     * @covers ::isLock
     * @covers ::isOptions
     * @covers ::isPatch
     * @covers ::isUnlock
     */
    public function testIs()
    {
        $this->assertTrue($this->request->isGet());
        $this->assertFalse($this->request->isPost());
        $this->assertFalse($this->request->isCopy());
        $this->assertFalse($this->request->isPut());
        $this->assertFalse($this->request->isDelete());
        $this->assertFalse($this->request->isHead());
        $this->assertFalse($this->request->isLock());
        $this->assertFalse($this->request->isOptions());
        $this->assertFalse($this->request->isPatch());
        $this->assertFalse($this->request->isUnlock());
    }

    /**
     * @covers ::getClientIp
     */
    public function testGetClientIp()
    {
        $this->assertEquals('192.168.1.1', $this->request->getClientIp());
    }

    /**
     * @covers ::getUri
     */
    public function testGetUri()
    {
        $this->assertEquals('/?test=test', $this->request->getUri());
    }

    /**
     * @covers ::getEncodings
     */
    public function testGetEncodings()
    {
        $this->assertEquals('gzip, deflate, sdch', $this->request->getEncodings());
    }
}