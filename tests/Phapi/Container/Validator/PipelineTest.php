<?php

namespace Phapi\Tests\Container\Validator;

use Phapi\Container;
use Phapi\Container\Validator\Pipeline as PipelineValidator;
use Phapi\Pipeline;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Container\Validator\Pipeline
 */
class PipelineTest extends TestCase
{

    public $validator;
    public $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->validator = new PipelineValidator($this->container);
    }

    public function testValidPipelineCallable()
    {
        $callable = function () {
            return new Pipeline();
        };

        $return = $this->validator->validate($callable);
        $this->assertSame($callable, $return);
    }

    public function testValidPipelineNotCallable()
    {
        $pipeline = new Pipeline();

        $return = $this->validator->validate($pipeline);
        $this->assertSame($pipeline, $return);
    }

    public function testInvalidPipeline()
    {
        $this->setExpectedException('RuntimeException', 'The configured pipeline does not implement the Pipeline Contract.');
        $return = $this->validator->validate(new \stdClass());
    }
}