<?php

namespace Phapi\Tests;

use Phapi\Dispatcher;
use Phapi\RouteParser;
use Phapi\Router;

/**
 * Mock of the router object
 *
 * Class RouterMock
 * @package Phapi\Tests
 */
class RouterMock extends Router
{

    protected $resource;
    protected $method;

    public function __construct($resource, $method)
    {
        $this->resource = $resource;
        $this->method = $method;
    }

    public function getMatchedResource()
    {
        return $this->resource;
    }

    public function getMatchedMethod()
    {
        return $this->method;
    }

}

/**
 * Mock Page object
 *
 * Class Page
 * @package Phapi\Tests
 */
class Page {

    public function get()
    {
        return [
            'resource' => 'called and returned'
        ];
    }

}

/**
 * @coversDefaultClass \Phapi\Dispatcher
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase {

    public $mock;

    public $router;

    public function setUp()
    {
        $routes = [
            '/page/{slug}/{id:[0-9]+}?' => '\\Phapi\\Tests\\Page',
        ];

        $this->router = new Router(new RouteParser(), $routes);
    }

    /**
     * @covers ::dispatch
     * @covers ::__construct
     */
    public function testDispatch()
    {
        $this->router->match('/page/someslug/37288', 'GET');

        $dispatcher = new Dispatcher($this->router);
        $this->assertEquals($dispatcher->dispatch(), ['resource' => 'called and returned']);
    }

    /**
     * @covers ::dispatch
     * @expectedException \Phapi\Exception\Error\MethodNotAllowed
     *
     * Exception is thrown in the router and not the dispatcher
     */
    public function testDispatchMethodNotAllowed()
    {
        $dispatcher = new Dispatcher(new RouterMock('\\Phapi\\Tests\\Page', 'PUT'));
        $dispatcher->dispatch();
    }

    /**
     * @covers ::dispatch
     * @expectedException \Phapi\Exception\Error\NotFound
     *
     * Exception is thrown in the router and not the dispatcher
     */
    public function testDispatchRouteNotFound()
    {
        $dispatcher = new Dispatcher(new RouterMock('NotExisting', 'PUT'));
        $dispatcher->dispatch();
    }
}