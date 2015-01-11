<?php

namespace Phapi;

/**
 * Content handler class (abstract)
 *
 * Abstract class outlining how content type handlers should work
 *
 * @category Serializer
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
abstract class Serializer
{

    /**
     * Content types that the handler can handle
     *
     * @var array
     */
    protected $contentTypes = [];

    /**
     * Extra content types only for output
     *
     * @var array
     */
    protected $acceptOnlyTypes = [];

    /**
     * The content type of the request
     *
     * @var string
     */
    protected $contentType = null;

    /**
     * The accept type for the response
     *
     * @var string
     */
    protected $accept = null;

    /**
     * Constructor
     *
     * It's possible to pass extra content/mime types that should be supported.
     * Good to use when, for example, adding vendor specific mime types.
     *
     * @param array $contentTypes       Extra content types that the serializer can both serialize and unserialize
     * @param array $acceptOnlyTypes    Extra content types that the serializer can only serialize
     */
    public function __construct($contentTypes = [], $acceptOnlyTypes = [])
    {
        $this->contentTypes = array_merge($this->contentTypes, $contentTypes);
        $this->acceptOnlyTypes = array_merge($this->acceptOnlyTypes, $acceptOnlyTypes);
    }

    /**
     * Set the content type that was sent from the client. This can be used
     * if the serializer needs to do something special with a specific content
     * type.
     *
     * @param $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Set the accept type that will be sent to the client. This can be used
     * if the serializer needs to do something like a JSON_PRETTY_PRINT on
     * text/html for example. This is useful if the api should display easy
     * to read response to browsers.
     *
     * @param $accept
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    /**
     * Check if the serializer can handle the specified content/mime type.
     * Second parameter defines if the check if for serialization or unserialization.
     * Default is to check for content types the serializer can unserialize (request).
     * Pass TRUE as the second parameter and the function will check for what content
     * types it can serialize.
     *
     * @param      $contentType
     * @param bool $serialize
     *
     * @return bool
     */
    public function supports($contentType, $serialize = false)
    {
        $match = array_search($contentType, $this->contentTypes);
        if ($serialize) {
            $match = array_search($contentType, array_merge($this->contentTypes, $this->acceptOnlyTypes));
        }

        return ($match === false) ? false: true;
    }

    /**
     * Get the content types that the serializer can handle. The parameter defines
     * if the check if for serialization or unserialization. Default is to check for
     * content types the serializer can unserialize (request). Pass TRUE as the
     * parameter and the function will check for what content types it can serialize.
     *
     * @param bool $serialize
     * @return array
     */
    public function getContentTypes($serialize = false)
    {
        if ($serialize) {
            return array_merge($this->contentTypes, $this->acceptOnlyTypes);
        }
        return $this->contentTypes;
    }

    /**
     * Abstract function. When implemented it should convert
     * the input (ex json, xml) to an php array.
     *
     * @abstract
     * @param $input
     * @return array
     */
    abstract public function unserialize($input);

    /**
     * Abstract function. When implemented it should convert
     * the input (array) to the content type (ex. json, xml).
     *
     * @abstract
     * @param $input
     * @return mixed
     */
    abstract public function serialize($input);

}