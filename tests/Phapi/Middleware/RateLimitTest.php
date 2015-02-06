<?php

namespace Phapi\Tests\Middleware;

use Phapi\Middleware\RateLimit;
use Phapi\Phapi;


/**
 * @coversDefaultClass \Phapi\Middleware\RateLimit
 */
class RateLimitTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::refillTokens
     * @covers ::setHeaders
     * @covers ::checkTokens
     * @covers ::getIdentifier
     */
    public function testMiddleware()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
            '\\Phapi\\Tests\\Page' => new RateLimit\Bucket(800, 60, 10, false),
        );

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi1',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Limit'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Remaining'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Window'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-New'));

        $this->assertEquals(800, $phapi->getResponse()->getHeaders()->get('X-Rate-Limit-Limit'));
        $this->assertEquals(10, $phapi->getResponse()->getHeaders()->get('X-Rate-Limit-Window'));
        $this->assertEquals(60, $phapi->getResponse()->getHeaders()->get('X-Rate-Limit-New'));
    }

    /**
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testNoCache()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
            '\\Phapi\\Tests\\Page' => new RateLimit\Bucket(800, 60, 10, false),
        );

        $phapi = new Phapi([
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi2',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
    }

    /**
     * @expectedException \Phapi\Exception\Error\InternalServerError
     */
    public function testNoBuckets()
    {
        $rateLimitBuckets = array();

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi3',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
    }

    /**
     * @covers ::__construct
     * @covers ::call
     * @covers ::refillTokens
     * @covers ::setHeaders
     * @covers ::checkTokens
     * @covers ::getIdentifier
     */
    public function testDefaultBucket()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
        );

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi4',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Limit'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Remaining'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Window'));
        $this->assertTrue($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-New'));

        $this->assertEquals(800, $phapi->getResponse()->getHeaders()->get('X-Rate-Limit-Limit'));
        // the following two assertions may seem wrong but since the default bucket values
        // is set to continuously add new tokens to the bucket this ends up with 1 second windows and
        // every second it adds ($newTokens = 400 divided by $newTokensWindow = 60)
        $this->assertEquals(1, $phapi->getResponse()->getHeaders()->get('X-Rate-Limit-Window'));
        $this->assertEquals(7, $phapi->getResponse()->getHeaders()->get('X-Rate-Limit-New'));
    }

    /**
     * no identifier
     */
    public function testNoIdentifier()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
            '\\Phapi\\Resource\\Page' => new RateLimit\Bucket(800, 60, 10, false),
        );

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Resource\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();

        $this->assertFalse($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Limit'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Remaining'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-Window'));
        $this->assertFalse($phapi->getResponse()->getHeaders()->has('X-Rate-Limit-New'));

        $this->assertEquals(null, $middleware->getIdentifier());
    }

    /**
     * @expectedException \Phapi\Exception\Error\TooManyRequests
     */
    public function testNoTokensContinuous()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
            '\\Phapi\\Tests\\Page' => new RateLimit\Bucket(1, 1, 1, true),
        );

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi1',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
        $middleware->call();
        $middleware->call();
    }

    /**
     * @expectedException \Phapi\Exception\Error\TooManyRequests
     */
    public function testNoTokensNotContinuous()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
            '\\Phapi\\Tests\\Page' => new RateLimit\Bucket(1, 1, 1, false),
        );

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi1',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->call();
        $middleware->call();
        $middleware->call();
    }
    /**
     * @covers ::getIdentifier
     * @covers ::setIdentifier
     */
    public function testGetIdentifier()
    {
        $rateLimitBuckets = array(
            'default' => new RateLimit\Bucket(),
            '\\Phapi\\Resource\\Page' => new RateLimit\Bucket(800, 60, 10, false),
        );

        $phapi = new Phapi([
            'cache'         => new \Phapi\Cache\Memcache(
                [
                    ['host' => 'localhost', 'port' => 11211]
                ]
            ),
            'server' => [
                'REQUEST_URI' => '/page/slug/3456',
                'HTTP_X_RATE_LIMIT_IDENTIFIER' => 'phapi5',
                'HTTP_ACCEPT' => 'application/json'
            ]
        ]);
        $phapi->getRouter()->setRoutes([
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Resource\\Page',
        ]);
        $phapi->getRouter()->match('/page/slug/3456', 'GET');

        $middleware = new RateLimit('X-Rate-Limit-Identifier', $rateLimitBuckets);
        $middleware->setApplication($phapi);
        $middleware->setNextMiddleware(new MockMiddleware());
        $middleware->setIdentifier('changedIdentifier');
        $middleware->call();

        $this->assertEquals('changedIdentifier', $middleware->getIdentifier());
        $this->assertNotEquals('phapi5', $middleware->getIdentifier());
    }

    /**
     * @covers ::setNextMiddleware
     * @covers ::getNextMiddleware
     */
    public function testNexMiddleware()
    {
        $middleware = new RateLimit('X-Rate-Limit-Identifier', []);
        $middleware->setNextMiddleware(new Phapi([]));
        $this->assertInstanceOf('\Phapi\Phapi', $middleware->getNextMiddleware());
    }

}