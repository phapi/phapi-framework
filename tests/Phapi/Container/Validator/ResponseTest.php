<?php

namespace Phapi\Tests\Container\Validator;

use Phapi\Container;
use Phapi\Container\Validator\Response as ResponseValidator;
use Phapi\Http\Response;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Container\Validator\Response
 */
class ResponseTest extends TestCase
{

    public $validator;
    public $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->validator = new ResponseValidator($this->container);
    }

    public function testValidPipelineCallable()
    {
        $callable = function () {
            return new Response();
        };

        $return = $this->validator->validate($callable);
        $this->assertSame($callable, $return);
    }

    public function testValidPipelineNotCallable()
    {
        $response = new Response();

        $return = $this->validator->validate($response);
        $this->assertSame($response, $return);
    }

    public function testInvalidPipeline()
    {
        $this->setExpectedException('RuntimeException', 'The configured response does not implement PSR-7.');
        $return = $this->validator->validate(new \stdClass());
    }
}