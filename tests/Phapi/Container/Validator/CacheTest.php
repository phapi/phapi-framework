<?php

namespace Phapi\Tests\Container\Validator;

use Phapi\Cache\Memcache;
use Phapi\Container;
use Phapi\Container\Validator\Cache;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \Phapi\Container\Validator\Log
 */
class CacheTest extends TestCase
{

    public $validator;
    public $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->container['log'] = new NullLogger();
        $this->validator = new Cache($this->container);
    }

    public function testInvalidCache()
    {
        $cache = $this->validator->validate(new \stdClass());
        $this->assertInstanceOf('Phapi\Cache\NullCache', $cache($this->container));

        //$this->setExpectedException('Exception', 'Unable to connect to Memcache backend');
    }

    public function testValidCache()
    {
        $closure = function ($app) {
            $cache = new \Phapi\Cache\Memcache($servers = [
                [
                    'host' => 'localhost',
                    'port' => 11211
                ]
            ]);
            $cache->connect();
            return $cache;
        };

        $cache = $this->validator->validate($closure);
        $this->assertInstanceOf('Phapi\Cache\Memcache', $cache($this->container));
    }

    public function testUnableToConnect()
    {
        $closure = function ($app) {
            $cache = new \Phapi\Cache\Memcache($servers = [
                [
                    'host' => 'localhost',
                    'port' => 1121
                ]
            ]);
            $cache->connect();
            return $cache;
        };

        $cache = $this->validator->validate($closure);
        $this->assertInstanceOf('Phapi\Cache\NullCache', $cache($this->container));
    }
}