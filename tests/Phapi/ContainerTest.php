<?php

namespace Phapi\Tests;

use Phapi\Container;
use Phapi\Tests\Fixtures\DicObject;

/**
 * @coversDefaultClass \Phapi\Container
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers ::__construct
     * @covers ::__set
     * @covers ::__get
     */
    public function testWithString()
    {
        $dic = new Container();
        $dic->param = 'value';

        $this->assertEquals('value', $dic->param);
    }

    /**
     * @covers ::__construct
     * @covers ::__set
     * @covers ::__get
     */
    public function testWithClosure()
    {
        $dic = new Container();
        $dic->dicObject = function () {
            return new DicObject();
        };

        $this->assertInstanceOf('Phapi\Tests\Fixtures\DicObject', $dic->dicObject);
    }

    /**
     * @covers ::__construct
     * @covers ::__set
     * @covers ::__get
     */
    public function testObjectsShouldBeSame()
    {
        $dic = new Container();
        $dic->dicObject = function () {
            return new DicObject();
        };

        $objectOne = $dic->dicObject;
        $this->assertInstanceOf('Phapi\Tests\Fixtures\DicObject', $objectOne);

        $objectTwo = $dic->dicObject;
        $this->assertInstanceOf('Phapi\Tests\Fixtures\DicObject', $objectTwo);

        $this->assertSame($objectOne, $objectTwo);
    }

    /**
     * @covers ::__construct
     * @covers ::__set
     * @covers ::__get
     * @covers ::factory
     */
    public function testObjectsShouldBeDifferent()
    {
        $dic = new Container();
        $dic->dicObject = $dic->factory(function () {
            return new DicObject();
        });

        $objectOne = $dic->dicObject;
        $this->assertInstanceOf('Phapi\Tests\Fixtures\DicObject', $objectOne);

        $objectTwo = $dic->dicObject;
        $this->assertInstanceOf('Phapi\Tests\Fixtures\DicObject', $objectTwo);

        $this->assertNotSame($objectOne, $objectTwo);
    }

    /**
     * @covers ::__isset
     * @covers ::__set
     */
    public function testIsset()
    {
        $dic = new Container();
        $dic->param = 'value';
        $dic->object = function () {
            return new Fixtures\DicObject();
        };

        $dic->null = null;

        $this->assertTrue(isset($dic->param));
        $this->assertTrue(isset($dic->object));
        $this->assertTrue(isset($dic->null));
        $this->assertFalse(isset($dic->non_existent));
    }

    /**
     * @covers ::__construct
     */
    public function testConstructorInjection()
    {
        $params = array("param" => "value");
        $dic = new Container($params);

        $this->assertSame($params['param'], $dic->param);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Identifier "foo" is not defined.
     */
    public function testOffsetGetValidatesKeyIsPresent()
    {
        $dic = new Container();
        echo $dic->foo;
    }

    /**
     * @covers ::__set
     * @covers ::__get
     */
    public function testOffsetGetHonorsNullValues()
    {
        $dic = new Container();
        $dic->foo = null;
        $this->assertNull($dic->foo);
    }

    /**
     * @covers ::__unset
     * @covers ::__set
     */
    public function testUnset()
    {
        $dic = new Container();
        $dic->param = 'value';
        $dic->object = function () {
            return new Fixtures\DicObject();
        };

        unset($dic->param, $dic->object);
        $this->assertFalse(isset($dic->param));
        $this->assertFalse(isset($dic->service));
    }

    /**
     * @covers ::__set
     */
    public function testGlobalFunctionNameAsParameterValue()
    {
        $dic = new Container();
        $dic->globalFunction = 'strlen';
        $this->assertSame('strlen', $dic->globalFunction);
    }

    /**
     * @covers ::__set
     * @covers ::__get
     */
    public function testDefiningNewServiceAfterFreeze()
    {
        $dic = new Container();
        $dic->foo = function () {
            return 'foo';
        };
        $foo = $dic->foo;

        $dic->bar = function () {
            return 'bar';
        };
        $this->assertSame('bar', $dic->bar);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Cannot override locked service "foo".
     * @covers ::__set
     */
    public function testOverridingServiceAfterFreeze()
    {
        $dic = new Container();
        $dic->foo = function () {
            return 'foo';
        };
        $foo = $dic->foo;

        $dic->foo = function () {
            return 'bar';
        };
    }

    /**
     * @covers ::__set
     * @covers ::__get
     */
    public function testRemovingServiceAfterFreeze()
    {
        $dic = new Container();
        $dic->foo = function () {
            return 'foo';
        };
        $foo = $dic->foo;

        unset($dic->foo);
        $dic->foo = function () {
            return 'bar';
        };
        $this->assertSame('bar', $dic->foo);
    }
}