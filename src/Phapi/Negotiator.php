<?php

namespace Phapi;

use Phapi\Exception\Error\NotAcceptable;
use Phapi\Exception\Error\UnsupportedMediaType;
use Phapi\Http\Request;
use Phapi\Http\Response;

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
class Negotiator {

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

    public function __construct($negotiator, $configuration, Request $request, Response $response)
    {
        // Set some properties
        $this->negotiator = $negotiator;
        $this->serializers = $configuration->get('serializers');
        $this->defaultAccept = $configuration->get('defaultAccept');
        $this->request = $request;
        $this->response = $response;
        $this->acceptHeader = $request->getHeaders()->get('accept', '');
        $this->contentTypeHeader = $request->getHeaders()->get('content-type', '');

        // Create a list of supported content types
        $this->createContentTypeList();

        // Negotiate formats
        $this->accept = $this->negotiateAccept();
        $this->contentType = $this->negotiateContentType();
    }

    /**
     * Handle the format negotiation of both the
     * accept and content type headers.
     *
     * @throws NotAcceptable
     * @throws UnsupportedMediaType
     */
    public function negotiate()
    {
        // Create variable that will be used if the provided accept type isn't supported
        $notAcceptable = false;

        // Check if the application can deliver the response in a format
        // that the client has asked for
        if (null === $accept = $this->getAccept()) {
            // If not, use the first type from the first configured serializer
            $accept = $this->defaultAccept;

            // Since we need to set the default accept value to be able to return something to client
            // we need to throw the exception later than now
            $notAcceptable = true;
        }

        // Save negotiated accept to the request
        $this->request->setAccept($accept);
        // Set the content type of the response
        $this->response->setContentType($accept);

        // Check if the client supplied an non acceptable accept type
        if ($notAcceptable) {
            throw new NotAcceptable(
                $this->request->getHeaders()->get('accept') .
                ' is not an supported Accept header. Supported types are: ' .
                implode(', ', $this->getAccepts())
            );
        }

        // Check if we have a body in the request
        if ($this->request->hasRawContent()) {
            // Check if the application can handle the format that the request body is in.
            if (null !== $contentType = $this->getContentType()) {
                $this->request->setContentType($contentType);
            } else {
                // The application can't handle this content type. Respond with a Not Acceptable response
                throw new UnsupportedMediaType(
                    $this->request->getHeaders()->get('content-type') .
                    ' is not an supported Content-Type header. Supported types are: ' .
                    implode(', ', $this->getContentTypes())
                );
            }
        }
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
                return $format->getValue();
            }
        }
        return null;
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
                return $format->getValue();
            }
        }
        return null;
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
                // Get all content types that the serializer can serialize and deserialize
                foreach ($serializer->getContentTypes() as $contentType) {
                    // Add to the list of deserializable content types
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