<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Request;
use Phapi\Http\Body as Stream;
use Phapi\Http\Uri;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Http\Request
 */
class RequestTest extends TestCase
{
    public function setUp()
    {
        $this->request = new Request();
    }

    public function testMethodIsNullByDefault()
    {
        $this->assertNull($this->request->getMethod());
    }

    public function testMethodMutatorReturnsCloneWithChangedMethod()
    {
        $request = $this->request->withMethod('GET');
        $this->assertNotSame($this->request, $request);
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testUriIsNullByDefault()
    {
        $this->assertNull($this->request->getUri());
    }

    public function testConstructorRaisesExceptionForInvalidStream()
    {
        $this->setExpectedException('InvalidArgumentException');
        new Request([],[],['TOTALLY INVALID']);
    }

    public function invalidUrls()
    {
        return [
            'null'   => [null],
            'true'   => [true],
            'false'  => [false],
            'int'    => [1],
            'float'  => [1.1],
            'array'  => [['foo']],
            'object' => [(object) ['foo']],
        ];
    }

    public function testWithUriReturnsNewInstanceWithNewUri()
    {
        $request = $this->request->withUri(new Uri('https://example.com:10082/foo/bar?baz=bat'));
        $this->assertNotSame($this->request, $request);
        $request2 = $request->withUri(new Uri('/baz/bat?foo=bar'));
        $this->assertNotSame($this->request, $request2);
        $this->assertNotSame($request, $request2);
        $this->assertEquals('/baz/bat?foo=bar', (string) $request2->getUri());
    }

    public function testConstructorCanAcceptAllMessageParts()
    {
        $body    = new Stream('php://memory');
        $headers = [
            'http_x_foo' => ['bar'],
        ];

        $server = [
            'REQUEST_URI' => 'http://example.com/',
            'REQUEST_METHOD' => 'POST',
            'HTTP_X_FOO' => ['bar']
        ];
        $request = new Request(
            $server,
            [],
            $body
        );

        $this->assertInstanceOf('Psr\Http\Message\UriInterface', $request->getUri());
        $this->assertSame('http://example.com/', (string) $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertSame($body, $request->getBody());
        $this->assertEquals($headers, $request->getHeaders());
    }

    public function invalidRequestUri()
    {
        return [
            'true'     => [ true ],
            'false'    => [ false ],
            'int'      => [ 1 ],
            'float'    => [ 1.1 ],
            'array'    => [ ['http://example.com'] ],
            'stdClass' => [ (object) [ 'href'         => 'http://example.com'] ],
        ];
    }

    /**
     * @dataProvider invalidRequestUri
     */
    public function testConstructorRaisesExceptionForInvalidUri($uri)
    {
        $this->setExpectedException('InvalidArgumentException', 'Unsupported URI');
        new Request(['REQUEST_URI' => $uri]);
    }

    public function invalidRequestMethod()
    {
        return [
            'true'       => [ true ],
            'false'      => [ false ],
            'int'        => [ 1 ],
            'float'      => [ 1.1 ],
            'bad-string' => [ 'BOGUS-METHOD' ],
            'array'      => [ ['POST'] ],
            'stdClass'   => [ (object) [ 'method' => 'POST'] ],
        ];
    }

    /**
     * @dataProvider invalidRequestMethod
     */
    public function testConstructorRaisesExceptionForInvalidMethod($method)
    {
        $this->setExpectedException('InvalidArgumentException', 'Unsupported HTTP method');
        new Request(['REQUEST_METHOD' => $method]);
    }

    public function invalidRequestBody()
    {
        return [
            'true'       => [ true ],
            'false'      => [ false ],
            'int'        => [ 1 ],
            'float'      => [ 1.1 ],
            'array'      => [ ['BODY'] ],
            'stdClass'   => [ (object) [ 'body' => 'BODY'] ],
        ];
    }

    /**
     * @dataProvider invalidRequestBody
     */
    public function testConstructorRaisesExceptionForInvalidBody($body)
    {
        $this->setExpectedException('InvalidArgumentException', 'stream');
        new Request([], [], $body);
    }

    public function testConstructorIgnoresInvalidHeaders()
    {
        $headers = [
            [ 'INVALID' ],
            'http_x_invalid_null' => null,
            'http_x_invalid_true' => true,
            'http_x_invalid_false' => false,
            'http_x_invalid_int' => 1,
            'http_x_invalid_object' => (object) ['INVALID'],
            'http_x_valid_string' => 'VALID',
            'http_x_valid_array' => [ 'VALID' ],
        ];
        $expected = [
            'http_x_valid_string' => [ 'VALID' ],
            'http_x_valid_array' => [ 'VALID' ],
        ];
        $request = new Request($headers);
        $this->assertEquals($expected, $request->getHeaders());
    }

    public function testRequestTargetIsSlashWhenNoUriPresent()
    {
        $request = new Request();
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function testRequestTargetIsSlashWhenUriHasNoPathOrQuery()
    {
        $request = (new Request())
            ->withUri(new Uri('http://example.com'));
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function requestsWithUri()
    {
        return [
            'absolute-uri' => [
                (new Request())
                    ->withUri(new Uri('https://api.example.com/user'))
                    ->withMethod('POST'),
                '/user'
            ],
            'absolute-uri-with-query' => [
                (new Request())
                    ->withUri(new Uri('https://api.example.com/user?foo=bar'))
                    ->withMethod('POST'),
                '/user?foo=bar'
            ],
            'relative-uri' => [
                (new Request())
                    ->withUri(new Uri('/user'))
                    ->withMethod('GET'),
                '/user'
            ],
            'relative-uri-with-query' => [
                (new Request())
                    ->withUri(new Uri('/user?foo=bar'))
                    ->withMethod('GET'),
                '/user?foo=bar'
            ],
        ];
    }

    /**
     * @dataProvider requestsWithUri
     */
    public function testReturnsRequestTargetWhenUriIsPresent($request, $expected)
    {
        $this->assertEquals($expected, $request->getRequestTarget());
    }

    public function validRequestTargets()
    {
        return [
            'asterisk-form'         => [ '*' ],
            'authority-form'        => [ 'api.example.com' ],
            'absolute-form'         => [ 'https://api.example.com/users' ],
            'absolute-form-query'   => [ 'https://api.example.com/users?foo=bar' ],
            'origin-form-path-only' => [ '/users' ],
            'origin-form'           => [ '/users?id=foo' ],
        ];
    }

    /**
     * @dataProvider validRequestTargets
     */
    public function testCanProvideARequestTarget($requestTarget)
    {
        $request = (new Request())->withRequestTarget($requestTarget);
        $this->assertEquals($requestTarget, $request->getRequestTarget());
    }

    public function testRequestTargetCannotContainWhitespace()
    {
        $request = new Request();
        $this->setExpectedException('InvalidArgumentException', 'Invalid request target');
        $request->withRequestTarget('foo bar baz');
    }

    public function testRequestTargetDoesNotCacheBetweenInstances()
    {
        $request = (new Request())->withUri(new Uri('https://example.com/foo/bar'));
        $original = $request->getRequestTarget();
        $newRequest = $request->withUri(new Uri('http://localhost/bar/baz'));
        $this->assertNotEquals($original, $newRequest->getRequestTarget());
    }

    public function testSettingNewUriResetsRequestTarget()
    {
        $request = (new Request())->withUri(new Uri('https://example.com/foo/bar'));
        $original = $request->getRequestTarget();
        $newRequest = $request->withUri(new Uri('http://mwop.net/bar/baz'));
    }

    public function testServerParamsAreEmptyByDefault()
    {
        $this->assertEmpty($this->request->getServerParams());
    }

    public function testQueryParamsAreEmptyByDefault()
    {
        $this->assertEmpty($this->request->getQueryParams());
    }

    public function testQueryParamsMutatorReturnsCloneWithChanges()
    {
        $value = ['foo' => 'bar'];
        $request = $this->request->withQueryParams($value);
        $this->assertNotSame($this->request, $request);
        $this->assertSame($value, $request->getQueryParams());
    }

    public function testCookiesAreEmptyByDefault()
    {
        $this->assertEmpty($this->request->getCookieParams());
    }

    public function testCookiesMutatorReturnsCloneWithChanges()
    {
        $value = ['foo' => 'bar'];
        $request = $this->request->withCookieParams($value);
        $this->assertSame($this->request, $request);
        $this->assertNotEquals($value, $request->getCookieParams());
    }

    public function testFileParamsAreEmptyByDefault()
    {
        $this->assertEmpty($this->request->getFileParams());
    }

    public function testParsedBodyIsEmptyByDefault()
    {
        $this->assertEmpty($this->request->getParsedBody());
    }

    public function testParsedBodyMutatorReturnsCloneWithChanges()
    {
        $value = ['foo' => 'bar'];
        $request = $this->request->withParsedBody($value);
        $this->assertNotSame($this->request, $request);
        $this->assertEquals($value, $request->getParsedBody());
    }

    public function testAttributesAreEmptyByDefault()
    {
        $this->assertEmpty($this->request->getAttributes());
    }

    /**
     * @depends testAttributesAreEmptyByDefault
     */
    public function testAttributeMutatorReturnsCloneWithChanges()
    {
        $request = $this->request->withAttribute('foo', 'bar');
        $this->assertNotSame($this->request, $request);
        $this->assertEquals('bar', $request->getAttribute('foo'));
        return $request;
    }

    /**
     * @depends testAttributeMutatorReturnsCloneWithChanges
     */
    public function testRemovingAttributeReturnsCloneWithoutAttribute($request)
    {
        $new = $request->withoutAttribute('foo');
        $this->assertNotSame($request, $new);
        $this->assertNull($new->getAttribute('foo', null));
    }

    public function testUsesProvidedConstructorArguments()
    {
        $server = [
            'foo' => 'bar',
            'baz' => 'bat',
        ];

        $server['server'] = true;

        $server['REQUEST_URI'] = 'http://example.com';
        $server['REQUEST_METHOD'] = 'POST';
        $server['HTTP_HOST'] = ['example.com'];

        $request = new Request(
            $server,
            [],
            'php://memory'
        );

        $this->assertEquals($server, $request->getServerParams());
        $this->assertEquals(null, $request->getFileParams());

        $this->assertInstanceOf('Psr\Http\Message\UriInterface', $request->getUri());
        $this->assertSame('http://example.com/', (string) $request->getUri());
        $this->assertEquals($server['REQUEST_METHOD'], $request->getMethod());
        $this->assertEquals(['http_host' => ['example.com']], $request->getHeaders());

        $body = $request->getBody();
        $r = new \ReflectionProperty($body, 'stream');
        $r->setAccessible(true);
        $stream = $r->getValue($body);
        $this->assertEquals('php://memory', $stream);
    }
}