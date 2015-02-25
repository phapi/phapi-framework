<?php
namespace Phapi\Tests\Http;

use Phapi\Http\Uri;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Http\Uri
 */
class UriTest extends TestCase
{

    /**
     * @covers ::__construct
     * @covers ::getScheme
     * @covers ::getUserInfo
     * @covers ::getHost
     * @covers ::getPort
     * @covers ::getAuthority
     * @covers ::getPath
     * @covers ::getQuery
     * @covers ::getFragment
     * @covers ::isStandardPort
     * @covers ::parse
     * @covers ::parseUserInfo
     */
    public function testConstructorSetsAllProperties()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user:pass', $uri->getUserInfo());
        $this->assertEquals('local.example.com', $uri->getHost());
        $this->assertEquals(3001, $uri->getPort());
        $this->assertEquals('user:pass@local.example.com:3001', $uri->getAuthority());
        $this->assertEquals('/foo', $uri->getPath());
        $this->assertEquals('bar=baz', $uri->getQuery());
        $this->assertEquals('quz', $uri->getFragment());
    }

    public function testCanSerializeToString()
    {
        $url = 'https://user:pass@local.example.com:3001/foo?bar=baz#quz';
        $uri = new Uri($url);
        $this->assertEquals($url, (string) $uri);
    }

    /**
     * @covers ::withScheme
     */
    public function testWithSchemeReturnsNewInstanceWithNewScheme()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withScheme('http');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('http', $new->getScheme());
        $this->assertEquals('http://user:pass@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers ::withUserInfo
     */
    public function testWithUserInfoReturnsNewInstanceWithProvidedUser()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withUserInfo('matthew');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('matthew', $new->getUserInfo());
        $this->assertEquals('https://matthew@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers ::withUserInfo
     */
    public function testWithUserInfoReturnsNewInstanceWithProvidedUserAndPassword()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withUserInfo('matthew', 'zf2');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('matthew:zf2', $new->getUserInfo());
        $this->assertEquals('https://matthew:zf2@local.example.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers ::withHost
     */
    public function testWithHostReturnsNewInstanceWithProvidedHost()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withHost('framework.zend.com');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('framework.zend.com', $new->getHost());
        $this->assertEquals('https://user:pass@framework.zend.com:3001/foo?bar=baz#quz', (string) $new);
    }

    /**
     * @covers ::withPort
     * @covers ::isStandardPort
     */
    public function testWithPortReturnsNewInstanceWithProvidedPort()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPort(3000);
        $this->assertNotSame($uri, $new);
        $this->assertEquals(3000, $new->getPort());
        $this->assertEquals('https://user:pass@local.example.com:3000/foo?bar=baz#quz', (string) $new);
    }

    public function invalidPorts()
    {
        return [
            'null'      => [ null ],
            'true'      => [ true ],
            'false'     => [ false ],
            'string'    => [ 'string' ],
            'array'     => [ [ 3000 ] ],
            'object'    => [ (object) [ 3000 ] ],
            'zero'      => [ 0 ],
            'too-small' => [ -1 ],
            'too-big'   => [ 65536 ],
        ];
    }

    /**
     * @dataProvider invalidPorts
     * @covers ::withPort
     * @covers ::isStandardPort
     */
    public function testWithPortRaisesExceptionForInvalidPorts($port)
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $this->setExpectedException('InvalidArgumentException', 'Invalid port');
        $new = $uri->withPort($port);
    }

    /**
     * @covers ::withPath
     */
    public function testWithPathReturnsNewInstanceWithProvidedPath()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withPath('/bar/baz');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('/bar/baz', $new->getPath());
        $this->assertEquals('https://user:pass@local.example.com:3001/bar/baz?bar=baz#quz', (string) $new);
    }

    public function invalidPaths()
    {
        return [
            'null'      => [ null ],
            'true'      => [ true ],
            'false'     => [ false ],
            'array'     => [ [ '/bar/baz' ] ],
            'object'    => [ (object) [ '/bar/baz' ] ],
            'query'     => [ '/bar/baz?bat=quz' ],
            'fragment'  => [ '/bar/baz#bat' ],
        ];
    }

    /**
     * @dataProvider invalidPaths
     * @covers ::withPath
     */
    public function testWithPathRaisesExceptionForInvalidPaths($path)
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $this->setExpectedException('InvalidArgumentException', 'Invalid path');
        $new = $uri->withPath($path);
    }

    /**
     * @covers ::withQuery
     */
    public function testWithQueryReturnsNewInstanceWithProvidedQuery()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withQuery('baz=bat');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('baz=bat', $new->getQuery());
        $this->assertEquals('https://user:pass@local.example.com:3001/foo?baz=bat#quz', (string) $new);
    }

    public function invalidQueryStrings()
    {
        return [
            'null'      => [ null ],
            'true'      => [ true ],
            'false'     => [ false ],
            'array'     => [ [ 'baz=bat' ] ],
            'object'    => [ (object) [ 'baz=bat' ] ],
            'fragment'  => [ 'baz=bat#quz' ],
        ];
    }

    /**
     * @dataProvider invalidQueryStrings
     * @covers ::withQuery
     */
    public function testWithQueryRaisesExceptionForInvalidQueryStrings($query)
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $this->setExpectedException('InvalidArgumentException', 'Query string');
        $new = $uri->withQuery($query);
    }

    /**
     * @covers ::withFragment
     * @covers ::getFragment
     */
    public function testWithFragmentReturnsNewInstanceWithProvidedFragment()
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $new = $uri->withFragment('qat');
        $this->assertNotSame($uri, $new);
        $this->assertEquals('qat', $new->getFragment());
        $this->assertEquals('https://user:pass@local.example.com:3001/foo?bar=baz#qat', (string) $new);
    }

    public function invalidFragments()
    {
        return [
            'null'      => [ null ],
            'true'      => [ true ],
            'false'     => [ false ],
            'array'     => [ [ '#quz' ] ],
            'object'    => [ (object) [ '#quz' ] ],
        ];
    }

    /**
     * @dataProvider invalidFragments
     * @covers ::withFragment
     */
    public function testWithFragmentRaisesExceptionForInvalidFragments($fragment)
    {
        $uri = new Uri('https://user:pass@local.example.com:3001/foo?bar=baz#quz');
        $this->setExpectedException('InvalidArgumentException', 'Fragment');
        $new = $uri->withFragment($fragment);
    }

    public function authorityInfo()
    {
        return [
            'host-only'      => [ 'http://foo.com/bar',         'foo.com' ],
            'host-port'      => [ 'http://foo.com:3000/bar',    'foo.com:3000' ],
            'user-host'      => [ 'http://me@foo.com/bar',      'me@foo.com' ],
            'user-host-port' => [ 'http://me@foo.com:3000/bar', 'me@foo.com:3000' ],
        ];
    }

    /**
     * @dataProvider authorityInfo
     * @covers ::isStandardPort
     * @covers ::getAuthority
     */
    public function testRetrievingAuthorityReturnsExpectedValues($url, $expected)
    {
        $uri = new Uri($url);
        $this->assertEquals($expected, $uri->getAuthority());
    }

    public function testCanEmitOriginFormUrl()
    {
        $url = '/foo/bar?baz=bat';
        $uri = new Uri($url);
        $this->assertEquals($url, (string) $uri);
    }

    /**
     * @covers ::withPath
     */
    public function testSettingEmptyPathOnAbsoluteUriIsEquivalentToSettingRootPath()
    {
        $uri = new Uri('http://example.com/foo');
        $new = $uri->withPath('');
        $this->assertEquals('/', $new->getPath());
    }

    /**
     * @covers ::__toString
     */
    public function testStringRepresentationOfAbsoluteUriWithNoPathNormalizesPath()
    {
        $uri = new Uri('http://example.com');
        $this->assertEquals('http://example.com/', (string) $uri);
    }

    /**
     * @covers ::getPath
     */
    public function testEmptyPathOnOriginFormIsEquivalentToRootPath()
    {
        $uri = new Uri('?foo=bar');
        $this->assertEquals('/', $uri->getPath());
    }

    /**
     * @covers ::__toString
     */
    public function testStringRepresentationOfOriginFormWithNoPathNormalizesPath()
    {
        $uri = new Uri('?foo=bar');
        $this->assertEquals('/?foo=bar', (string) $uri);
    }

    public function invalidConstructorUris()
    {
        return [
            'null' => [ null ],
            'true' => [ true ],
            'false' => [ false ],
            'int' => [ 1 ],
            'float' => [ 1.1 ],
            'array' => [ [ 'http://example.com/' ] ],
            'object' => [ (object) [ 'uri' => 'http://example.com/' ] ],
        ];
    }

    /**
     * @dataProvider invalidConstructorUris
     * @covers ::__construct
     */
    public function testConstructorRaisesExceptionForNonStringURI($uri)
    {
        $this->setExpectedException('InvalidArgumentException');
        new Uri($uri);
    }

    /**
     * @covers ::withScheme
     */
    public function testMutatingSchemeStripsOffDelimiter()
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withScheme('https://');
        $this->assertEquals('https', $new->getScheme());
    }

    public function invalidSchemes()
    {
        return [
            'mailto' => [ 'mailto' ],
            'ftp'    => [ 'ftp' ],
            'telnet' => [ 'telnet' ],
            'ssh'    => [ 'ssh' ],
            'git'    => [ 'git' ],
        ];
    }

    /**
     * @dataProvider invalidSchemes
     * @covers ::withScheme
     * @param $scheme
     */
    public function testMutatingWithNonWebSchemeRaisesAnException($scheme)
    {
        $uri = new Uri('http://example.com');
        $this->setExpectedException('InvalidArgumentException', 'Unsupported scheme');
        $uri->withScheme($scheme);
    }

    /**
     * @covers ::withPath
     */
    public function testPathIsPrefixedWithSlashIfSetWithoutOne()
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withPath('foo/bar');
        $this->assertEquals('/foo/bar', $new->getPath());
    }

    /**
     * @covers ::withQuery
     */
    public function testStripsQueryPrefixIfPresent()
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withQuery('?foo=bar');
        $this->assertEquals('foo=bar', $new->getQuery());
    }

    /**
     * @covers ::withFragment
     */
    public function testStripsFragmentPrefixIfPresent()
    {
        $uri = new Uri('http://example.com');
        $new = $uri->withFragment('#/foo/bar');
        $this->assertEquals('/foo/bar', $new->getFragment());
    }

    public function standardSchemePortCombinations()
    {
        return [
            'http'  => [ 'http', 80 ],
            'https' => [ 'https', 443 ],
            'empty' => [ '', 80 ],
        ];
    }

    /**
     * @dataProvider standardSchemePortCombinations
     * @covers ::withHost
     * @covers ::withScheme
     * @covers ::withPort
     * @covers ::isStandardPort
     */
    public function testAuthorityOmitsPortForStandardSchemePortCombinations($scheme, $port)
    {
        $uri = (new Uri())
            ->withHost('example.com')
            ->withScheme($scheme)
            ->withPort($port);
        $this->assertEquals('example.com', $uri->getAuthority());
    }
}