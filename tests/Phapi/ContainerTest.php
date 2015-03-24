<?php

namespace Phapi\Tests;

use Phapi\Container;
use Phapi\Tests\Fixtures\ContainerValidator;
use Phapi\Tests\Fixtures\DicObject;

/**
 * @coversDefaultClass \Phapi\Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testWithString()
    {
        $container = new Container();
        $container->bind('param', 'value');

        $this->assertEquals('value', $container->make('param'));
    }

    public function testWithStringAndObject()
    {
        $container = new Container();
        $container->bind('param', 'container value');

        $container->bind('dicObject', function ($container) {
            $obj = new DicObject();
            $obj->value = $container->make('param');
            return $obj;
        });

        $one = $container->make('dicObject');
        $two = $container->make('dicObject');

        $this->assertSame($one, $two);
        $this->assertSame('container value', $one->value);
        $this->assertSame('container value', $two->value);
    }

    public function testSingleton()
    {
        $container = new Container();
        $container->bind('object', function ($container) {
            $obj = new DicObject();
            $obj->value = 'singleton test';
            return $obj;
        }, Container::TYPE_MULTITON);

        $one = $container->make('object');
        $two = $container->make('object');

        $this->assertNotSame($one, $two);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Identifier "Foo" is not defined.
     */
    public function testMakeInvalidKey()
    {
        $container = new Container();
        $container->make('Foo');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot override locked content "dicObject".
     */
    public function testLocked()
    {
        $container = new Container();
        $container->bind('param', 'container value');

        $container->bind('dicObject', function ($container) {
            $obj = new DicObject();
            $obj->value = $container->make('param');
            return $obj;
        });

        $object = $container->make('dicObject');

        $container->bind('dicObject', 'something');
    }

    public function testArrayAccess()
    {
        $container = new Container();
        $container['param'] = 'value';
        $this->assertEquals('value', $container['param']);
        $this->assertTrue(isset($container['param']));
        unset($container['param']);
        $this->assertFalse(isset($container['param']));
    }

    public function testValidator()
    {
        $container = new Container();
        $container->addValidator('test', new ContainerValidator($container));
        $container->bind('test', 'value');
        $this->assertEquals('value', $container['test']);
    }
}