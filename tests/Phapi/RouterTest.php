<?php

namespace Phapi\Tests;


use Phapi\Cache\Memcache;
use Phapi\RouteParser;
use Phapi\Router;

/**
 * @coversDefaultClass \Phapi\Router
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{

    public $routes;

    public function setUp()
    {
        $this->routes = [
            '/' => '\\Phapi\\Tests\\Home',
            '/users' => '\\Phapi\\Tests\\Users',
            '/users/{name:a}' => '\\Phapi\\Tests\\User',
            '/articles/{id:[0-9]+}' => '\\Phapi\\Tests\\Article',
            '/color/{id:h}' => '\\Phapi\\Tests\\Color',
            '/products/{name}' => '\\Phapi\\Tests\\Product',
            '/products/' => '\\Phapi\\Tests\\Products',
            '/blog/{date:c}?/{title:c}?' => '\\Phapi\\Tests\\Blog\\Post',
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::setCache
     */
    public function testConstructor()
    {
        $router = new Router(new RouteParser(), []);
        $this->assertInstanceOf('\\Phapi\\Router', $router);

        $cache = new Memcache([['host' => 'localhost', 'port' => 11211]]);
        $cache->connect();
        $cache->flush();
        $router->setCache($cache);

        return $router;
    }

    /**
     * @depends testConstructor
     * @covers ::match
     * @covers ::resourceMethodExists
     *
     * @param Router $router
     * @return Router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatch(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/page/someslug/37288', 'GET');
        return $router;
    }

    /**
     * @expectedException \Phapi\Exception\Error\NotFound
     *
     * @covers ::match
     * @covers ::matchCache
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testCacheMatchFail()
    {
        $router = new Router(new RouteParser(), []);
        $router->addRoutes($this->routes);

        $this->assertInstanceOf('\\Phapi\\Router', $router);

        $cache = new Memcache([['host' => 'localhost', 'port' => 11211]]);
        $cache->connect();
        $cache->flush();

        $cached = [
            '/color/54/' => [
                'matchedRoute' => '/color/{id:h}',
                'matchedResource' => '\\Phapi\\Tests\\Color'
            ]
        ];

        $cache->set('routes', $cached);
        $router->setCache($cache);
        $router->match('/color/54', 'GET');
    }

    /**
     * @expectedException \Phapi\Exception\Error\MethodNotAllowed
     *
     * @covers ::match
     * @covers ::matchCache
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testCacheMatchFail2()
    {
        $router = new Router(new RouteParser(), []);
        $router->addRoutes($this->routes);

        $this->assertInstanceOf('\\Phapi\\Router', $router);

        $cache = new Memcache([['host' => 'localhost', 'port' => 11211]]);
        $cache->connect();
        $cache->flush();

        $cached = [
            '/users/phapi/' => [
                'matchedRoute' => '/users/{name:a}',
                'matchedResource' => '\\Phapi\\Tests\\Users'
            ]
        ];

        $cache->set('routes', $cached);
        $router->setCache($cache);
        $router->match('/users/phapi', 'PUT');
    }

    /**
     * @depends testConstructor
     * @covers ::setRoutes
     * @covers ::addRoutes
     * @covers ::addRoute
     * @covers ::getRoutes
     *
     * @param Router $router
     */
    public function testSetRoutes(Router $router)
    {
        // add default set of routes
        $router->addRoutes($this->routes);
        $this->assertEquals($router->getRoutes(), $this->routes);

        // add a new dummy route to change route table
        $router->addRoute('/help', '\\Phapi\\Resource\\Help');
        $this->assertNotEquals($router->getRoutes(), $this->routes);

        // (re)set default routes
        $router->setRoutes($this->routes);
        $this->assertEquals($router->getRoutes(), $this->routes);
    }

    /**
     * @depends testConstructor
     * @covers ::addRoutes
     * @covers ::addRoute
     *
     * @param Router $router
     * @return Router
     */
    public function testAddRoutes(Router $router)
    {
        $router->addRoutes($this->routes);
        return $router;
    }

    /**
     * @depends testAddRoutes
     * @covers ::getRoutes
     *
     * @param Router $router
     */
    public function testGetRoutes(Router $router)
    {
        $this->assertEquals($router->getRoutes(), $this->routes);
    }

    /**
     * @depends testMatch
     * @covers ::getMatchedMethod
     *
     * @param Router $router
     */
    public function testGetMatchedMethod(Router $router)
    {
        $this->assertEquals($router->getMatchedMethod(), 'GET');
        $this->assertNotEquals($router->getMatchedMethod(), 'POST');
    }

    /**
     * @depends testMatch
     * @covers ::getMatchedResource
     *
     * @param Router $router
     */
    public function testGetMatchedResource(Router $router)
    {
        $this->assertEquals('\\Phapi\\Tests\\Page', $router->getMatchedResource());
        $this->assertNotEquals('\\Paphi\\Resource\\Users', $router->getMatchedResource());
    }

    /**
     * @depends testMatch
     * @covers ::getMatchedRoute
     *
     * @param Router $router
     */
    public function testGetMatchedRoute(Router $router)
    {
        $this->assertEquals('/page/{slug}/{id:[0-9]+}?', $router->getMatchedRoute());
        $this->assertNotEquals('/articles/{id:[0-9]+}', $router->getMatchedRoute());
    }

    /**
     * @depends testMatch
     * @covers ::getParams
     *
     * @param Router $router
     */
    public function testGetParams(Router $router)
    {
        $this->assertEquals(['slug' => 'someslug', 'id' => '37288'], $router->getParams());
        $this->assertNotEquals(['id' => '37288'], $router->getParams());
    }

    /**
     * @depends testConstructor
     *
     * @param Router $router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatchMore(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/users', 'GET');
        $this->assertEquals('\\Phapi\\Tests\\Users', $router->getMatchedResource());

        $router->match('/articles/100', 'GET');
        $this->assertEquals('\\Phapi\\Tests\\Article', $router->getMatchedResource());

        $router->match('/articles/100', 'GET');
        $this->assertEquals('\\Phapi\\Tests\\Article', $router->getMatchedResource());
    }

    /**
     * @depends testConstructor
     * @expectedException \Phapi\Exception\Error\NotFound
     *
     * @param Router $router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatchRouteNotFound(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/products', 'GET');
    }

    /**
     * @depends testConstructor
     * @expectedException \Phapi\Exception\Error\NotFound
     *
     * @param Router $router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatchRouteNotFound2(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/nonexisting', 'GET');
    }

    /**
     * @depends testConstructor
     * @expectedException \Phapi\Exception\Error\NotFound
     *
     * @param Router $router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatchRouteNotFound3(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/blog/2014-03-01/the-title', 'GET');
    }

    /**
     * @depends testConstructor
     * @expectedException \Phapi\Exception\Error\MethodNotAllowed
     *
     * @param Router $router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatchMethodNotAllowed(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/users', 'PUT');
    }

    /**
     * @depends testConstructor
     * @expectedException \Phapi\Exception\Error\MethodNotAllowed
     *
     * @param Router $router
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     * @throws \Phapi\Exception\Error\NotFound
     */
    public function testMatchMethodNotAllowed2(Router $router)
    {
        $router->addRoutes($this->routes);

        $router->match('/articles/189', 'PUT');
    }
}


// Mock Resources

class Users {

    public function get()
    {
        return [
            'resource' => 'called and returned'
        ];
    }
}

class Article {

    public function get()
    {
        return [
            'article' => 'read it'
        ];
    }
}