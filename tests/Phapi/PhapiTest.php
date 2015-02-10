<?php

namespace Phapi\Tests;

use Phapi\Cache\Memcache;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Exception\Redirect\MovedPermanently;
use Phapi\Exception\Success\Ok;
use Phapi\Http\Response;
use Phapi\Middleware\Cors;
use Phapi\Phapi;
use Phapi\Resource;
use Phapi\Serializer\Json;
use Phapi\Serializer\Jsonp;
use Psr\Log\NullLogger;


/**
 * @coversDefaultClass \Phapi\Phapi
 */
class PhapiTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::getDefaultConfiguration
     * @covers ::setCache
     * @covers ::deserializeBody
     * @covers ::addCallbackToJsonPSerializer
     */
    public function testConstruct()
    {
        $phapi = new Phapi([
            'httpVersion' => '1.1',
            'mode' => Phapi::MODE_DEVELOPMENT,
            'rawContent' => '{ "foo": "bar" }'
        ]);

        $this->assertEquals('1.1', $phapi->configuration->get('httpVersion', null));
    }

    /**
     * @expectedException \Phapi\Exception\Error\NotAcceptable
     */
    public function testNegotiateNotAcceptable()
    {
        $phapi = new Phapi([
            'serializers' => [ new Jsonp() ]
        ]);
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
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());
    }

    /**
     * @covers ::setLogWriter
     * @covers ::getLogWriter
     */
    public function testSetLogWriter2()
    {
        $phapi = new Phapi([ 'logWriter' => new \stdClass() ]);
        $this->assertInstanceOf('Psr\Log\NullLogger', $phapi->getLogWriter());
    }

    /**
     * @covers ::setLogWriter
     * @covers ::getLogWriter
     */
    public function testSetLogWriter3()
    {
        $phapi = new Phapi([ 'logWriter' => new NullLogger() ]);
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

    /**
     * @covers ::exceptionHandler
     * @covers ::getSerializer
     */
    public function testExceptionHandlerSuccess()
    {
        $phapi = new Phapi(['serializers' => [new Json(), new Jsonp()]]);
        $phapi->getResponse()->setBody(['content' => 'array']);
        $phapi->exceptionHandler(new Ok());
        // Use response object to check status code
        $this->assertEquals(200, $phapi->getResponse()->getStatus());
        $this->assertEquals(['content' => 'array'], $phapi->getResponse()->getBody());
    }

    /**
     * @covers ::exceptionHandler
     */
    public function testExceptionHandlerRedirect()
    {
        $phapi = new Phapi([]);
        $phapi->getResponse()->setBody(['content' => 'array']);
        $phapi->exceptionHandler(new MovedPermanently('http://www.github.com'));
        // Use response object to check status code
        $this->assertEquals(301, $phapi->getResponse()->getStatus());
        $this->assertEquals([], $phapi->getResponse()->getBody());
        $this->assertEquals('http://www.github.com', $phapi->getResponse()->getLocation());
    }

    /**
     * @covers ::exceptionHandler
     * @covers ::prepareErrorBody
     * @covers ::logErrorException
     */
    public function testExceptionHandlerError()
    {
        $phapi = new Phapi([]);
        $phapi->getResponse()->setBody(['content' => 'array']);
        $phapi->exceptionHandler(new InternalServerError('Could not connect to database', 23, null, 'http://docs.localhost'));
        // Use response object to check status code
        $this->assertEquals(500, $phapi->getResponse()->getStatus());

        $expectedArray = [
            'errors' => [
                'message' => 'Could not connect to database',
                'code' => 23,
                'description' => 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix it as soon as possible.',
                'link' => 'http://docs.localhost'
            ]
        ];

        $this->assertEquals($expectedArray, $phapi->getResponse()->getBody());
    }

    /**
     * @covers ::exceptionHandler
     * @covers ::prepareErrorBody
     * @covers ::logErrorException
     */
    public function testExceptionHandlerUnknownException()
    {
        $phapi = new Phapi([]);
        $phapi->getResponse()->setBody(['content' => 'array']);
        $phapi->exceptionHandler(new \Exception('Could not connect to database', 23));
        // Use response object to check status code
        $this->assertEquals(500, $phapi->getResponse()->getStatus());

        $expectedArray = [
            'errors' => [
                'message' => 'Could not connect to database',
                'code' => 23,
                'description' => 'An internal server error occurred. Please try again within a few minutes. The error has been logged and we have been notified about the problem and we will fix it as soon as possible.',
            ]
        ];

        $this->assertEquals($expectedArray, $phapi->getResponse()->getBody());
    }

    /**
     * @covers ::getRouter
     */
    public function testGetRouter()
    {
        $phapi = new Phapi([]);
        $this->assertInstanceOf('Phapi\Router', $phapi->getRouter());
    }

    /**
     * @covers ::getNegotiator
     */
    public function testGetNegotiator()
    {
        $phapi = new Phapi([]);
        $this->assertInstanceOf('Phapi\Negotiator', $phapi->getNegotiator());
    }

    /**
     * @covers ::run
     * @covers ::call
     * @expectedException \Phapi\Exception\Success\Ok
     */
    public function testRun()
    {
        $phapi = new Phapi([]);
        $phapi->getRouter()->addRoutes([ '/' => '\\Phapi\\Tests\\Home' ]);
        $phapi->run();
    }

    /**
     * @covers ::addCallbackToJsonPSerializer
     */
    public function testAddCallbackToJsonPSerializer()
    {
        $config = [
            'server' => [
                'HTTP_ACCEPT' => 'application/javascript',
            ],
            'get' => [
                'callback' => 'someFunction'
            ]
        ];
        $phapi = new Phapi($config);
    }

    /**
     * @covers ::addCallbackToJsonPSerializer
     */
    public function testAddCallbackToJsonPSerializer2()
    {
        $config = [
            'server' => [
                'HTTP_ACCEPT' => 'application/javascript',
                'CONTENT_TYPE' => 'application/json'
            ],
            'rawContent' => '{ "callback": "someFunction" }'

        ];
        $phapi = new Phapi($config);
    }

    /**
     * @covers ::run
     * @covers ::call
     * @expectedException \Phapi\Exception\Success\Created
     */
    public function testCallCreated()
    {
        $phapi = new Phapi([
            'server' => [
                'REQUEST_METHOD' => 'POST',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->addRoutes([ '/' => '\\Phapi\\Tests\\Home' ]);
        $phapi->run();
    }

    /**
     * @covers ::run
     * @covers ::call
     * @expectedException \Phapi\Exception\Success\Accepted
     */
    public function testCallAccepted()
    {
        $phapi = new Phapi([
            'server' => [
                'REQUEST_METHOD' => 'PUT',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->addRoutes([ '/' => '\\Phapi\\Tests\\Home' ]);
        $phapi->run();
    }

    /**
     * @covers ::run
     * @covers ::call
     * @expectedException \Phapi\Exception\Success\Ok
     */
    public function testCallOther()
    {
        $phapi = new Phapi([
            'server' => [
                'REQUEST_METHOD' => 'DELETE',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->addRoutes([ '/' => '\\Phapi\\Tests\\Home' ]);
        $phapi->run();
    }

    /**
     * @covers ::addMiddleware
     * @expectedException \Phapi\Exception\Success\Ok
     */
    public function testAddMiddleware()
    {
        $options = [
            'allowedOrigins' => ['*'],
            'allowedMethods' => ['*'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => ['Request-ID'],
            'maxAge' => 3600,
            'supportsCredentials' => true,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => '/'
            ]
        ]);
        $phapi->addMiddleware(new Cors($options));
        $phapi->getRouter()->addRoutes([ '/' => '\\Phapi\\Tests\\Home' ]);
        $phapi->run();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Vary'));

        $this->assertEquals('*', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Origin'));
        $this->assertEquals('Request-ID', $phapi->getResponse()->getHeaders()->get('Access-Control-Expose-Headers'));
        $this->assertEquals('true', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Credentials'));
    }
}

class Home extends Resource {

    public function get()
    {
        return [ 'key' => 'value' ];
    }

    public function post()
    {
        return [ 'body' => [], 'status' => Response::STATUS_CREATED];
    }

    public function put()
    {
        return [ 'body' => [], 'status' => Response::STATUS_ACCEPTED];
    }

    public function delete()
    {
        return [ 'body' => [], 'status' => Response::STATUS_NO_CONTENT];
    }
}