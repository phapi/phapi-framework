<?php

namespace Phapi\Tests;

use Phapi\Phapi;
use Phapi\Resource;

/**
 * @coversDefaultClass \Phapi\Resource
 */
class ResourceTest extends \PHPUnit_Framework_TestCase {

    /**
     * @covers ::__construct
     * @covers ::options
     * @covers ::parseMethodDoc
     * @covers ::prepareOutput
     */
    public function testConstruct()
    {
        $expected = [
            'contentTypes' => [
                'application/json',
                'text/json',
                'application/javascript',
                'application/x-www-form-urlencoded'
            ],
            'accept' => [
                'application/json',
                'text/json',
                'application/javascript',
                'application/x-www-form-urlencoded'
            ],
            'methods' => [
                'GET' => [
                    'uri' => '/blog/12',
                    'description' => 'Retrieve the blogs information like id, name and description',
                    'params' => 'id int',
                    'response' => [
                        'id int Blog ID',
                        'name string The name of the blog',
                        'description string A description of the blog',
                        'links string A list of links'
                    ]
                ]
            ]
        ];

        $resource = new Blog(new Phapi([]));
        $this->assertInstanceOf('Phapi\Resource', $resource);
        $this->assertEquals($expected, $resource->options());
    }

    /**
     * @covers ::__construct
     * @covers ::options
     *
     * @expectedException \Phapi\Exception\Error\MethodNotAllowed
     *
     * @throws \Phapi\Exception\Error\MethodNotAllowed
     */
    public function testException()
    {
        $resource = new Blog(new Phapi([]));
        $resource->changeResponse();
        $resource->options();
    }
}

class Blog extends Resource
{
    /**
     * @apiUri /blog/12
     * @apiDescription Retrieve the blogs information like
     *                 id, name and description
     * @apiParams id int
     * @apiResponse id int Blog ID
     * @apiResponse name string The name of the blog
     * @apiResponse description string A description of the blog
     * @apiResponse links string
     *              A list of links
     */
    public function get()
    {
        return [
            'id' => 12,
            'name' => 'Dev blog'
        ];
    }

    /**
     * Change response to null to test exception
     */
    public function changeResponse()
    {
        $this->response = null;
    }
}