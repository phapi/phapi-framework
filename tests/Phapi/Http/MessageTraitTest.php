<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Request;
use Phapi\Http\Body as Stream;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Http\Request
 */
class MessageTraitTest extends TestCase
{
    public function setUp()
    {
        $this->stream  = new Stream('php://memory', 'wb+');
        $this->message = new Request([], [], $this->stream);
    }

    public function testProtocolHasAcceptableDefault()
    {
        $this->assertEquals('1.1', $this->message->getProtocolVersion());
    }

    public function testProtocolMutatorReturnsCloneWithChanges()
    {
        $message = $this->message->withProtocolVersion('1.0');
        $this->assertNotSame($this->message, $message);
        $this->assertEquals('1.0', $message->getProtocolVersion());
    }

    public function testWithProtocolException()
    {
        $this->setExpectedException('InvalidArgumentException', 'Unsupported HTTP protocol version');
        $message = $this->message->withProtocolVersion('2.3');
    }

    public function testUsesStreamProvidedInConstructorAsBody()
    {
        $this->assertSame($this->stream, $this->message->getBody());
    }

    public function testBodyMutatorReturnsCloneWithChanges()
    {
        $stream  = new Stream('php://memory', 'wb+');
        $message = $this->message->withBody($stream);
        $this->assertNotSame($this->message, $message);
        $this->assertSame($stream, $message->getBody());
    }

    public function testGetHeaderLinesReturnsHeaderValueAsArray()
    {
        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);
        $this->assertNotSame($this->message, $message);
        $this->assertEquals(['Foo', 'Bar'], $message->getHeaderLines('X-Foo'));
        $this->assertEquals([], $message->getHeaderLines('X-Bar'));
    }

    public function testGetHeaderReturnsHeaderValueAsCommaConcatenatedString()
    {
        $message = $this->message->withHeader('X-Foo', ['Foo', 'Bar']);
        $this->assertNotSame($this->message, $message);
        $this->assertEquals('Foo,Bar', $message->getHeader('X-Foo'));
    }

    public function testHasHeaderReturnsFalseIfHeaderIsNotPresent()
    {
        $this->assertFalse($this->message->hasHeader('X-Foo'));
    }

    public function testHasHeaderReturnsTrueIfHeaderIsPresent()
    {
        $message = $this->message->withHeader('X-Foo', 'Foo');
        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasHeader('X-Foo'));
    }

    public function testAddHeaderAppendsToExistingHeader()
    {
        $message  = $this->message->withHeader('X-Foo', 'Foo');
        $this->assertNotSame($this->message, $message);
        $message2 = $message->withAddedHeader('X-Foo', 'Bar');
        $this->assertNotSame($message, $message2);
        $this->assertEquals('Foo,Bar', $message2->getHeader('X-Foo'));
    }

    public function testCanRemoveHeaders()
    {
        $message = $this->message->withHeader('X-Foo', 'Foo');
        $this->assertNotSame($this->message, $message);
        $this->assertTrue($message->hasHeader('x-foo'));
        $message2 = $message->withoutHeader('x-foo');
        $this->assertNotSame($this->message, $message2);
        $this->assertNotSame($message, $message2);
        $this->assertFalse($message2->hasHeader('X-Foo'));
    }

    public function invalidGeneralHeaderValues()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'int'    => [1],
            'float'  => [1.1],
            'array'  => [[ 'foo' => [ 'bar' ] ]],
            'object' => [(object) [ 'foo' => 'bar' ]],
        ];
    }

    /**
     * @dataProvider invalidGeneralHeaderValues
     */
    public function testSetHeaderRaisesExceptionForInvalidNestedHeaderValue($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'Unsupported header value');
        $message = $this->message->withHeader('X-Foo', [ $value ]);
    }

    public function invalidHeaderValues()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'int'    => [1],
            'float'  => [1.1],
            'object' => [(object) [ 'foo' => 'bar' ]],
        ];
    }

    /**
     * @dataProvider invalidHeaderValues
     */
    public function testSetHeaderRaisesExceptionForInvalidValueType($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'Unsupported header value');
        $message = $this->message->withHeader('X-Foo', $value);
    }

    /**
     * @dataProvider invalidHeaderValues
     */
    public function testAddHeaderRaisesExceptionForNonStringNonArrayValue($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'must be a string');
        $message = $this->message->withAddedHeader('X-Foo', 'value');
        $message = $message->withAddedHeader('X-Foo', $value);
    }

    public function testRemoveHeaderDoesNothingIfHeaderDoesNotExist()
    {
        $this->assertFalse($this->message->hasHeader('X-Foo'));
        $message = $this->message->withoutHeader('X-Foo');
        $this->assertSame($this->message, $message);
        $this->assertFalse($message->hasHeader('X-Foo'));
    }
}