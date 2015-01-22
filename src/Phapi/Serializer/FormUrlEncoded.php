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
class FormUrlEncoded extends Serializer
{

    /**
     * Mime types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'application/x-www-form-urlencoded'
    ];

    /**
     * Converts input to an php array
     *
     * @param $input
     * @return array|mixed
     */
    public function deserialize($input)
    {
        parse_str($input, $array);
        return $array;
    }

    /**
     * Converts an array to form url encoded format
     *
     * @param $input
     * @return mixed|string
     */
    public function serialize($input)
    {
        return http_build_query($input);
    }
}