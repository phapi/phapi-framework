<?php
namespace Phapi\Tests;

use Phapi\Http\Request;
use Phapi\Http\Response;
use Phapi\Pipeline;
use Phapi\Tests\Fixtures\MiddlewareObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Pipeline
 */
class PipelineTest extends TestCase
{

    public function testConstructor()
    {
        $request = new Request();
        $response = new Response();

        $pipeline = new Pipeline(new \stdClass());
        $pipeline->pipe(new MiddlewareObject());
        $pipeline->pipe(function ($request, $response, $next) {
            $response = $next($request, $response, $next);
            return $response->withStatus(500);
        });
        $response = $pipeline($request, $response, $pipeline);

        $this->assertTrue($response->hasHeader('X-Foo'));
        $this->assertEquals('modified', $response->getHeader('X-Foo'));
        $this->assertSame(500, $response->getStatusCode());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Middleware can’t be added once the stack is dequeuing
     */
    public function testLock()
    {
        $request = new Request();
        $response = new Response();

        $pipeline = new Pipeline(new \stdClass());
        $pipeline->pipe(new MiddlewareObject());
        $pipeline->pipe(function ($request, $response, $next) use ($pipeline) {
            $pipeline->pipe(new MiddlewareObject());
            $response = $next($request, $response, $next);
            return $response->withStatus(500);
        });
        $response = $pipeline($request, $response, $pipeline);
    }
}