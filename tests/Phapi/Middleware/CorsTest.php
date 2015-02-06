<?php

namespace Phapi\Tests\Middleware;

use Phapi\Middleware;
use Phapi\Middleware\Cors;
use Phapi\Phapi;


/**
 * @coversDefaultClass \Phapi\Middleware\Cors
 */
class CorsTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testNoCorsCall()
    {
        $options = [
            'allowedOrigins' => ['*'],
            'allowedMethods' => ['*'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => [],
            'maxAge' => 3600,
            'supportsCredentials' => false,
        ];

        $phapi = new Phapi([

        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Vary'));
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testCorsCall()
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
                'REQUEST_METHOD' => 'GET'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

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

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testCorsOriginCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.bar'],
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
                'REQUEST_METHOD' => 'GET'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Vary'));

        $this->assertEquals('http://foo.bar', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Origin'));
        $this->assertEquals('Request-ID', $phapi->getResponse()->getHeaders()->get('Access-Control-Expose-Headers'));
        $this->assertEquals('true', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Credentials'));
        $this->assertEquals('Origin', $phapi->getResponse()->getHeaders()->get('Vary'));
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testCorsOriginFailCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.fail'],
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
                'REQUEST_METHOD' => 'GET'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testCorsMethodFailCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.bar'],
            'allowedMethods' => ['GET', 'POST', 'OPTIONS'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => ['Request-ID'],
            'maxAge' => 3600,
            'supportsCredentials' => true,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'PUT'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testCorsMethodPassCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.bar'],
            'allowedMethods' => ['GET', 'POST', 'OPTIONS'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => ['Request-ID'],
            'maxAge' => 3600,
            'supportsCredentials' => true,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'POST'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Vary'));

        $this->assertEquals('http://foo.bar', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Origin'));
        $this->assertEquals('Request-ID', $phapi->getResponse()->getHeaders()->get('Access-Control-Expose-Headers'));
        $this->assertEquals('true', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Credentials'));
        $this->assertEquals('Origin', $phapi->getResponse()->getHeaders()->get('Vary'));
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createRequestHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testCorsVaryCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.bar'],
            'allowedMethods' => ['GET', 'POST', 'OPTIONS'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => ['Request-ID'],
            'maxAge' => 3600,
            'supportsCredentials' => true,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'POST'
            ]
        ]);

        $phapi->getResponse()->addHeaders(['Vary' => 'Accept-Encoding']);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Vary'));

        $this->assertEquals('http://foo.bar', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Origin'));
        $this->assertEquals('Request-ID', $phapi->getResponse()->getHeaders()->get('Access-Control-Expose-Headers'));
        $this->assertEquals('true', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Credentials'));
        $this->assertEquals('Accept-Encoding, Origin', $phapi->getResponse()->getHeaders()->get('Vary'));
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createPreflightHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testPreflightCall()
    {
        $options = [
            'allowedOrigins' => ['*'],
            'allowedMethods' => ['*'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => [],
            'maxAge' => 3600,
            'supportsCredentials' => false,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'OPTIONS',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'X-Rate-Limit-Identifier'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Vary'));

        $this->assertEquals('*', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Origin'));
        $this->assertEquals('GET', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Methods'));
        $this->assertEquals('3600', $phapi->getResponse()->getHeaders()->get('Access-Control-Max-Age'));
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createPreflightHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testPreflightOriginFailCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.bar'],
            'allowedMethods' => ['*'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => [],
            'maxAge' => 3600,
            'supportsCredentials' => false,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.fail',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'OPTIONS',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createPreflightHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     * @expectedException \Phapi\Exception\Error\BadRequest
     */
    public function testPreflightMissingHeaderCall()
    {
        $options = [
            'allowedOrigins' => ['http://foo.bar'],
            'allowedMethods' => ['*'],
            'allowedHeaders' => ['*'],
            'exposedHeaders' => [],
            'maxAge' => 3600,
            'supportsCredentials' => false,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'OPTIONS'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::createPreflightHeaders
     * @covers ::createOriginHeader
     * @covers ::createCredentialsHeader
     * @covers ::createExposedHeadersHeader
     * @covers ::createAllowedMethodsHeader
     * @covers ::createAllowedHeadersHeader
     * @covers ::createMaxAgeHeader
     * @covers ::createVaryHeader
     * @covers ::checkOrigin
     * @covers ::checkMethod
     * @covers ::prepareOptions
     *
     * @throws \Phapi\Exception\Error\BadRequest
     */
    public function testPreflightRequestHeadersCall()
    {
        $options = [
            'allowedOrigins' => ['*'],
            'allowedMethods' => ['*'],
            'allowedHeaders' => ['X-Rate-Limit-Identifier'],
            'exposedHeaders' => [],
            'maxAge' => 3600,
            'supportsCredentials' => false,
        ];

        $phapi = new Phapi([
            'server' => [
                'HTTP_ORIGIN' => 'http://foo.bar',
                'HTTP_ACCEPT' => 'application/json',
                'REQUEST_METHOD' => 'OPTIONS',
                'HTTP_ACCESS_CONTROL_REQUEST_METHOD' => 'GET',
                'HTTP_ACCESS_CONTROL_REQUEST_HEADERS' => 'X-Rate-Limit-Identifier'
            ]
        ]);

        $middleware = new Cors($options);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Origin'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Credentials'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Access-Control-Expose-Headers'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Methods'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Allow-Headers'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('Access-Control-Max-Age'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('Vary'));

        $this->assertEquals('*', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Origin'));
        $this->assertEquals('GET', $phapi->getResponse()->getHeaders()->get('Access-Control-Allow-Methods'));
        $this->assertEquals('3600', $phapi->getResponse()->getHeaders()->get('Access-Control-Max-Age'));
    }
}

class MockMiddleware extends Middleware {

    public function call()
    {

    }
}