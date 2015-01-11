<?php

namespace Phapi\Tests;

use Phapi\Cache\Memcache;
use Phapi\Phapi;
use Psr\Log\NullLogger;


/**
 * @coversDefaultClass \Phapi\Phapi
 */
class PhapiTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::getDefaultConfiguration
     * @covers ::setCache
     */
    public function testConstruct()
    {
        $phapi = new Phapi([
            'httpVersion' => '1.1',
            'mode' => Phapi::MODE_DEVELOPMENT
        ]);
        $this->assertEquals('1.1', $phapi->configuration->get('httpVersion', null));
    }

    /**
     * @covers ::setCache
     * @covers ::getCache
     */
    public function testSetGetCache()
    {
        $phapi = new Phapi([
            'cache' => new Memcache([['host' => 'localhost', 'port' => 11211]]),
        ]);
        $this->assertInstanceOf('Phapi\Cache\Memcache', $phapi->getCache());
    }

    /**
     * @covers ::getRequest
     */
    public function testGetRequest()
    {
        $phapi = new Phapi([]);
        $this->assertInstanceOf('Phapi\Http\Request', $phapi->getRequest());
    }

    /**
     * @covers ::getResponse
     */
    public function testGetResponse()
    {
        $phapi = new Phapi([]);
        $this->assertInstanceOf('Phapi\Http\Response', $phapi->getResponse());
    }

    /**
     * @covers ::setCache
     * @covers ::getCache
     */
    public function testSetGetCache2()
    {
        $phapi = new Phapi([
            'cache' => new Memcache([['host' => 'localhost', 'port' => 11111]]),
        ]);
        $this->assertInstanceOf('Phapi\Cache\NullCache', $phapi->getCache());
    }

    /**
     * @covers ::setLogWriter
     * @covers ::getLogWriter
     */
    public function testSetLogWriter()
    {
        $phapi = new Phapi([]);
        $phapi->setLogWriter(null);
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());

        $phapi->setLogWriter(new \stdClass());
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());

        $phapi->setLogWriter(new NullLogger());
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_WARNING, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler2()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_USER_ERROR, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler3()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_USER_WARNING, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler4()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_USER_NOTICE, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler5()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_STRICT, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler6()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_RECOVERABLE_ERROR, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler7()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_DEPRECATED, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler8()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_USER_DEPRECATED, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler9()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_NOTICE, 'message', 'index.php', 23, []);
    }

    /**
     * @covers ::errorHandler
     * @throws \Phapi\Exception\Error\InternalServerError
     */
    public function testErrorHandler10()
    {
        $phapi = new Phapi([]);

        $this->setExpectedException('Phapi\Exception\Error\InternalServerError');
        $phapi->errorHandler(E_COMPILE_ERROR, 'message', 'index.php', 23, []);
    }
}
