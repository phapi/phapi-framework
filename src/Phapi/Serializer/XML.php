<?php

namespace Phapi\Serializer;

use Phapi\Exception\Error\BadRequest;
use Phapi\Exception\Error\InternalServerError;
use Phapi\Serializer;

/**
 * XML Serializer
 *
 * Class handling XML content
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class XML extends Serializer
{

    /**
     * Content types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [
        'application/xml'
    ];

    /**
     * Converts a XML document to an php array
     *
     * @param $input
     * @return mixed
     * @throws BadRequest
     */
    public function deserialize($input)
    {
        try {
            $xml = simplexml_load_string($input);
            if (null === $array = json_decode(json_encode($xml), true)) {
                throw new BadRequest('Could not deserialize XML');
            }
        } catch (\Exception $e) {
            throw new BadRequest('Could not deserialize XML');
        }
        return $array;
    }

    /**
     * Converts an array to a XML document
     *
     * @param $input
     * @return string
     * @throws InternalServerError
     */
    public function serialize($input)
    {
        // creating object of SimpleXMLElement
        $xml = new \SimpleXMLElement("<?xml version=\"1.0\"?><response></response>");

        try {
            // function call to convert array to xml
            $this->arrayToXML($input, $xml);
        } catch (\Exception $e) {
            throw new InternalServerError('Could not serialize data to XML');
        }

        return $xml->asXML();
    }

    /**
     * Convert array to xml
     *
     * @param $input
     * @param $xml
     */
    private function arrayToXML($input, $xml)
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $key = is_numeric($key) ? "item$key" : $key;
                $subNode = $xml->addChild("$key");
                $this->arrayToXML($value, $subNode);
            } else {
                $key = is_numeric($key) ? "item$key" : $key;
                $xml->addChild("$key", "$value");
            }
        }
    }
}
