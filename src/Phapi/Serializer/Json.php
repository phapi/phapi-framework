<?php

namespace Phapi\Serializer;

use Phapi\Exception\Error\BadRequest;
use Phapi\Exception\Error\InternalServerError;
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
     * Converts a JSON document to an php array
     *
     * @param $input
     * @return mixed
     * @throws BadRequest
     */
    public function deserialize($input)
    {
        if (null === $array = json_decode($input, true)) {
            throw new BadRequest('Could not deserialize Json');
        }
        return $array;
    }

    /**
     * Converts an array to a JSON document
     *
     * @param $input
     * @return string
     * @throws InternalServerError
     */
    public function serialize($input)
    {
        if (false === $json = json_encode($input)) {
            throw new InternalServerError('Could not serialize data to Json');
        }
        return $json;
    }
}
