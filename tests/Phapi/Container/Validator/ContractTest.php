<?php

namespace Phapi\Tests\Container\Validator;

use Phapi\Container;
use Phapi\Container\Validator\Contract as ContractValidator;
use Phapi\Pipeline;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @coversDefaultClass \Phapi\Container\Validator\Contract
 */
class ContractTest extends TestCase
{

    public $validator;
    public $container;

    public function setUp()
    {
        $this->container = new Container();
        $this->validator = new ContractValidator($this->container);
        $this->validator->setContract('Phapi\Contract\Pipeline');
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
        $this->setExpectedException('RuntimeException', 'The configured value does not implement');
        $return = $this->validator->validate(new \stdClass());
    }
}