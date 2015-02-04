<?php

namespace Phapi\Tests;

use Negotiation\FormatNegotiator;
use Phapi\Bucket;
use Phapi\Http\Header;
use Phapi\Http\Request;
use Phapi\Http\Response;
use Phapi\Negotiator;
use Phapi\Serializer\Json;
use Phapi\Serializer\Jsonp;

/**
 * @coversDefaultClass \Phapi\Negotiator
 */
class NegotiationTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::negotiateAccept
     * @covers ::negotiateContentType
     * @covers ::createContentTypeList
     * @covers ::getContentType
     * @covers ::getAccept
     * @covers ::getContentTypes
     * @covers ::getAccepts
     * @covers ::negotiate
     */
    public function testConstruct()
    {
        $serializers = [ new Json([], ['text/html']), new Jsonp() ];
        $contentTypeHeader = 'application/json';
        $acceptHeader = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $server = [ 'CONTENT_TYPE' => $contentTypeHeader, 'HTTP_ACCEPT' => $acceptHeader];

        $request = new Request(null, null, $server, '{ "foo": "bar"}');

        $negotiation = new Negotiator(new FormatNegotiator(), new Bucket(['serializers' => $serializers, 'defaultAccept' => 'application/json']), $request, new Response(new Header()));
        $negotiation->negotiate();

        $this->assertEquals('application/json', $negotiation->getContentType());
        $this->assertEquals('text/html', $negotiation->getAccept());
        $this->assertEquals([
            'application/json',
            'text/json',
            'text/html',
            'application/javascript',
            'text/javascript',
        ], $negotiation->getAccepts());
        $this->assertEquals([
            'application/json',
            'text/json',
            'application/javascript',
            'text/javascript',
        ], $negotiation->getContentTypes());
    }

    /**
     * @covers ::__construct
     * @covers ::negotiateAccept
     * @covers ::negotiateContentType
     * @covers ::createContentTypeList
     * @covers ::getContentType
     * @covers ::getAccept
     */
    public function testFail()
    {
        $serializers = [ new Json([], ['text/html']), new Jsonp() ];
        $contentTypeHeader = 'application/xml';
        $acceptHeader = 'application/xhtml+xml,application/xml;q=0.9,image/webp';

        $server = [ 'CONTENT_TYPE' => $contentTypeHeader, 'HTTP_ACCEPT' => $acceptHeader];

        $request = new Request(null, null, $server, null);

        $negotiation = new Negotiator(new FormatNegotiator(), new Bucket(['serializers' => $serializers, 'defaultAccept' => 'application/json']), $request, new Response(new Header()));

        $this->assertEquals(null, $negotiation->getContentType());
        $this->assertEquals(null, $negotiation->getAccept());
    }

    /**
     * @covers ::negotiate
     * @expectedException \Phapi\Exception\Error\UnsupportedMediaType
     *
     * @throws \Phapi\Exception\Error\NotAcceptable
     * @throws \Phapi\Exception\Error\UnsupportedMediaType
     */
    public function testFail2()
    {
        $serializers = [ new Json([], ['text/html']), new Jsonp() ];
        $contentTypeHeader = 'application/xml';
        $acceptHeader = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';

        $server = [ 'CONTENT_TYPE' => $contentTypeHeader, 'HTTP_ACCEPT' => $acceptHeader];

        $request = new Request(null, null, $server, '{ "foo": "bar"}');

        $negotiation = new Negotiator(new FormatNegotiator(), new Bucket(['serializers' => $serializers, 'defaultAccept' => 'application/json']), $request, new Response(new Header()));
        $negotiation->negotiate();

        $this->assertEquals('application/json', $negotiation->getContentType());
        $this->assertEquals('text/html', $negotiation->getAccept());
        $this->assertEquals([
            'application/json',
            'text/json',
            'text/html',
            'application/javascript'
        ], $negotiation->getAccepts());
        $this->assertEquals([
            'application/json',
            'text/json',
            'application/javascript'
        ], $negotiation->getContentTypes());
    }
}