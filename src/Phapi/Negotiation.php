<?php

namespace Phapi;

/**
 * Content handler class (abstract)
 *
 * Abstract class outlining how content type handlers should work
 *
 * @category Negotiation
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Negotiation {

    /**
     * The format negotiator
     *
     * @var mixed
     */
    protected $negotiator;

    /**
     * Serializers
     *
     * @var array
     */
    protected $serializers;

    /**
     * The accept header
     *
     * @var null|string
     */
    protected $acceptHeader;

    /**
     * The content type header
     *
     * @var null|string
     */
    protected $contentTypeHeader;

    /**
     * Supported content types
     *
     * @var array
     */
    protected $contentTypes = [];

    /**
     * Supported accept types
     *
     * @var array
     */
    protected $accepts = [];

    /**
     * The negotiated content type (the result)
     *
     * @var string|null
     */
    protected $contentType;

    /**
     * The negotiated accept (the result)
     *
     * @var string|null
     */
    protected $accept;

    public function __construct($negotiator, $serializers, $acceptHeader = null, $contentTypeHeader = null)
    {
        // Set some properties
        $this->negotiator = $negotiator;
        $this->serializers = $serializers;
        $this->acceptHeader = $acceptHeader;
        $this->contentTypeHeader = $contentTypeHeader;

        // Create a list of supported content types
        $this->createContentTypeList();

        // Negotiate formats
        $this->negotiateAccept();
        $this->negotiateContentType();
    }

    /**
     * Negotiate the format of the accept header
     */
    protected function negotiateAccept()
    {
        // Check if we have an accept header to work with
        if ($this->acceptHeader !== null) {
            // Do some format negotiation
            $format = $this->negotiator->getBest($this->acceptHeader, $this->accepts);

            // Save the result
            if ($format !== null) {
                $this->accept = $format->getValue();
            }
        }
    }

    /**
     * Negotiate the format of the content type header
     */
    protected function negotiateContentType()
    {
        // Check if we have an accept header to work with
        if ($this->contentTypeHeader !== null) {
            // Do some format negotiation
            $format = $this->negotiator->getBest($this->contentTypeHeader, $this->contentTypes);

            // Save the result
            if ($format !== null) {
                $this->contentType = $format->getValue();
            }
        }
    }

    /**
     * Create a list of content types the application can handle.
     */
    protected function createContentTypeList()
    {
        // Loop through all serializers
        foreach ($this->serializers as $serializer) {
            // Make sure it's a valid serializer
            if ($serializer instanceof Serializer) {
                // Get all content types that the serializer can serialize and unserialize
                foreach ($serializer->getContentTypes() as $contentType) {
                    // Add to the list of unserializable content types
                    $this->contentTypes[] = $contentType;
                }
                // Get all content types that the serializer can only serialize
                foreach ($serializer->getContentTypes(true) as $acceptType) {
                    // Add to the list of serializable content types
                    $this->accepts[] = $acceptType;
                }
            }
        }
    }

    /**
     * Get negotiated accept type (the result)
     *
     * @return mixed
     */
    public function getAccept()
    {
        return $this->accept;
    }

    /**
     * Get negotiated content type (the result)
     *
     * @return mixed
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get all supported content types
     *
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * Get all supported accept types
     *
     * @return array
     */
    public function getAccepts()
    {
        return $this->accepts;
    }
}