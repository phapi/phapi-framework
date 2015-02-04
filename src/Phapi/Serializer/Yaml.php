<?php

namespace Phapi\Serializer;

use Phapi\Exception\Error\BadRequest;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Serializer;

/**
 * PHP Serializer
 *
 * Class handling Yaml content
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Yaml extends Serializer
{

    /**
     * Content types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'application/x-yaml',
        'text/x-yaml',
        'text/yaml'
    ];

    /**
     * Converts a serialized array to an deserialized array
     *
     * @param $input
     * @return mixed
     * @throws BadRequest
     */
    public function deserialize($input)
    {
        try {
            $result = \Symfony\Component\Yaml\Yaml::parse($input);
        } catch (\Exception $e) {
            throw new BadRequest('Could not deserialize Yaml');
        }
        return $result;
    }

    /**
     * Converts an array to a serialized array
     *
     * @param $input
     * @return string
     * @throws InternalServerError
     */
    public function serialize($input)
    {
        try {
            $output = \Symfony\Component\Yaml\Yaml::dump($input, $inline = 2, $indent = 4, $exceptionOnInvalidType = true);
        } catch (\Exception $e) {
            throw new InternalServerError('Could not serialize Yaml');
        }

        return $output;
    }
}
