<?php

namespace Phapi\Serializer;

use Phapi\Serializer;

/**
 * JSON Serializer
 *
 * Class handling JSON content
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Json extends Serializer
{

    /**
     * Content types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'application/json',
        'text/json'
    ];

    /**
     * Extra content types only for output
     *
     * @var array
     */
    protected $acceptOnlyTypes = [];

    /**
     * Converts a JSON document to an php array
     *
     * @param $input
     * @return array|mixed
     */
    public function unserialize($input)
    {
        return json_decode($input, true);
    }

    /**
     * Converts an array to a JSON document
     *
     * @param $input
     * @return mixed|string
     */
    public function serialize($input)
    {
        if (in_array($this->accept, $this->acceptOnlyTypes)) {
            return json_encode($input, JSON_PRETTY_PRINT);
        }
        return json_encode($input);
    }
}
