<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Http;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamableInterface;
use Psr\Http\Message\UriInterface;

/**
 * Representation of an incoming, server-side HTTP request.
 *
 * @category Phapi
 * @package  Phapi\Http
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Request implements ServerRequestInterface {

    use MessageTrait;

    /**
     * Server params
     *
     * @var array
     */
    protected $serverParams;

    /**
     * Request method
     *
     * @var null|string
     */
    protected $method;

    /**
     * Attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * Parsed body
     *
     * @var mixed
     */
    protected $parsedBody;

    /**
     * Query parameters
     *
     * @var array
     */
    protected $queryParams;

    /**
     * Request target
     *
     * @var string
     */
    protected $requestTarget;

    /**
     * Request URI
     *
     * @var null|Uri
     */
    protected $uri;

    /**
     * Valid request methods
     *
     * @var array
     */
    protected $validMethods = [
        'GET', 'HEAD', 'OPTIONS',
        'POST', 'PATCH', 'PUT',
        'DELETE', 'COPY',
        'LOCK', 'UNLOCK'
    ];

    public function __construct(
        array $serverParams = [],
        array $queryParams = [],
        $body = 'php://input',
        $validMethods = []
    ) {
        $this->serverParams = $serverParams;
        $this->getQueryParams = $queryParams;

        $this->protocol =
            (isset($serverParams['SERVER_PROTOCOL'])) ?
            substr($serverParams['SERVER_PROTOCOL'], -3)
            : '1.1';

        $this->body = ($body instanceof StreamableInterface) ? $body : new Body($body);
        $this->uri = (isset($this->serverParams['REQUEST_URI'])) ? new Uri($this->serverParams['REQUEST_URI']) : null;

        $this->headers = $this->findHeaders($this->serverParams);

        if (!empty($validMethods)) {
            $this->validMethods = $validMethods;
        }

        $this->method = $this->findMethod($this->serverParams);
    }

    /**
     * Retrieve server parameters.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * Cookies should not be used with APIs
     *
     * @return null
     */
    public function getCookieParams()
    {
        return null;
    }

    /**
     * Cookies should not be used with APIs. Will return same object
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return self
     */
    public function withCookieParams(array $cookies)
    {
        return $this;
    }

    /**
     * Retrieve query string arguments.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Create a new instance with the specified query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return self
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;
        return $clone;
    }

    /**
     * Files should not be used with APIs. Use PUT requests and body instead
     *
     * @return array Upload file(s) metadata, if any.
     */
    public function getFileParams()
    {
        return null;
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    /**
     * Create a new instance with the specified body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return self
     */
    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        return (array_key_exists($name, $this->attributes)) ? $this->attributes[$name] : $default;
    }

    /**
     * Create a new instance with the specified derived request attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return self
     */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * Create a new instance that removes the specified derived request
     * attribute.
     *
     * @see getAttributes()
     * @param string $name The attribute name.
     * @return self
     */
    public function withoutAttribute($name)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $this;
        }

        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }

    /**
     * Retrieves the message's request target.
     *
     * @return string
     */
    public function getRequestTarget()
    {
        if (null !== $this->requestTarget) {
            return $this->requestTarget;
        }

        if (!$this->uri) {
            return '/';
        }

        $this->requestTarget = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $this->requestTarget .= '?' . $this->uri->getQuery();
        }
        return $this->requestTarget;
    }

    /**
     * Create a new instance with a specific request-target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-2.7 (for the various
     *     request-target forms allowed in request messages)
     * @param mixed $requestTarget
     * @return self
     */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new \InvalidArgumentException(
                'Invalid request target provided, cannot contain whitespace'
            );
        }

        $clone = clone $this;
        $clone->requestTarget = $requestTarget;
        return $clone;
    }

    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Find the request method from server params
     *
     * @param $serverParams
     * @return null|string
     */
    protected function findMethod($serverParams)
    {
        if (!isset($serverParams['REQUEST_METHOD'])) {
            return null;
        }

        $method = $serverParams['REQUEST_METHOD'];

        // check for x-http-method-override header
        if ($method === 'POST' && ($override = $this->getHeader('X-HTTP-METHOD-OVERRIDE'))) {
            $method = strtoupper($override);
        }

        if (!in_array($method, $this->validMethods, true)) {
            throw new \InvalidArgumentException(
                'Unsupported HTTP method; supported methods: '. implode(', ', $this->validMethods)
            );
        }

        return $method;
    }

    /**
     * Create a new instance with the provided HTTP method.
     *
     * @param string $method Case-insensitive method.
     * @return self
     * @throws \InvalidArgumentException for invalid HTTP methods.
     */
    public function withMethod($method)
    {
        if (!in_array($method, $this->validMethods)) {
            throw new \InvalidArgumentException(
                'Unsupported request method; supported methods: '. implode(', ', $this->validMethods)
            );
        }

        $clone = clone $this;
        $clone->method = $method;
        return $clone;
    }

    /**
     * Retrieves the URI instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @return UriInterface Returns a UriInterface instance
     *     representing the URI of the request, if any.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Create a new instance with the provided URI.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param UriInterface $uri New request URI to use.
     * @return self
     */
    public function withUri(UriInterface $uri)
    {
        $clone = clone $this;
        $clone->requestTarget = null;
        $clone->uri = $uri;
        return $clone;
    }

    /**
     * Find headers in the server parameters
     *
     * @param array $serverParams
     * @return array
     */
    protected function findHeaders($serverParams = [])
    {
        $headers = [];
        foreach ($serverParams as $key => $value) {
            if (!$this->isValidHeader($key, $value)) {
                continue;
            }

            $key = strtolower($key);
            if (
                0 === strpos($key, 'http_') ||
                isset($this->specialHeaders[$key])
            ) {
                $headers[$key] = (is_array($value)) ? $value : [$value];
            }
        }

        return $headers;
    }


}