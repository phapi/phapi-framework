<?php

namespace Phapi\Tests;

use Negotiation\FormatNegotiator;
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
     */
    public function testConstruct()
    {
        $serializers = [ new Json([], ['text/html']), new Jsonp() ];
        $contentTypeHeader = 'application/json';
        $acceptHeader = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
        $negotiation = new Negotiator(new FormatNegotiator(), $serializers, $acceptHeader, $contentTypeHeader);

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
        $negotiation = new Negotiator(new FormatNegotiator(), $serializers, $acceptHeader, $contentTypeHeader);

        $this->assertEquals(null, $negotiation->getContentType());
        $this->assertEquals(null, $negotiation->getAccept());
    }
}