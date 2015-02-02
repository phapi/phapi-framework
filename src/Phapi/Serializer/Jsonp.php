<?php

namespace Phapi\Serializer;

use Phapi\Exception\Error\BadRequest;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Serializer;

/**
 * JSONP Serializer
 *
 * Class handling JSONP content
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Jsonp extends Serializer
{

    /**
     * Mime types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'application/javascript'
    ];

    /**
     * Callback function
     *
     * @var null|string
     */
    protected $callback = null;

    /**
     * Set callback
     *
     * @param $callback
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;
    }

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
            throw new BadRequest('Could not deserialize Jsonp');
        }
        return $array;
    }

    /**
     * Converts an array to a JSON document
     *
     * @param $input
     * @return mixed|string
     * @throws InternalServerError
     */
    public function serialize($input)
    {
        if ($this->callback !== null) {
            if (preg_match('/\W/', $this->callback)) {
                // if $callback contains a non-word character, this could be an XSS attack.
                $this->callback = null;
            }
        }

        if (false === $json = json_encode($input)) {
            throw new InternalServerError('Could not serialize data to Jsonp');
        }

        return ($this->callback !== null) ? $this->callback . '(' . $json . ')': $json;
    }
}
