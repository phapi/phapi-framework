<?php

namespace Phapi\Tests\Container\Validator;

use Phapi\Container;
use Phapi\Container\Validator\Request as RequestValidator;
use Phapi\Http\Request;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Container\Validator\Request
 */
class RequestTest extends TestCase
{

    public $validator;
    public $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->validator = new RequestValidator($this->container);
    }

    public function testValidPipelineCallable()
    {
        $callable = function () {
            return new Request();
        };

        $return = $this->validator->validate($callable);
        $this->assertSame($callable, $return);
    }

    public function testValidPipelineNotCallable()
    {
        $request = new Request();

        $return = $this->validator->validate($request);
        $this->assertSame($request, $return);
    }

    public function testInvalidPipeline()
    {
        $this->setExpectedException('RuntimeException', 'The configured request does not implement PSR-7.');
        $return = $this->validator->validate(new \stdClass());
    }
}