<?php

namespace Phapi\Serializer;

use Phapi\Exception\Error\BadRequest;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Serializer;

/**
 * PHP Serializer
 *
 * Class handling XML content
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class PHP extends Serializer
{

    /**
     * Content types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'text/x-php',
        'application/x-php'
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
            if (false === $result = unserialize($input)) {
                throw new BadRequest('Could not deserialize PHP');
            }
        } catch (\Exception $e) {
            throw new BadRequest('Could not deserialize PHP');
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
            $output = serialize($input);
        } catch (\Exception $e) {
            throw new InternalServerError('Could not serialize PHP');
        }

        return $output;
    }
}
