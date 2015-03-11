<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Http;

use Psr\Http\Message\StreamableInterface;

trait MessageTrait {

    /**
     * Protocol version
     *
     * @var string
     */
    protected $protocol = '1.1';

    /**
     * List of valid protocol versions
     *
     * @var array
     */
    protected $validProtocols = ['1.0', '1.1', '2.0'];

    /**
     * Headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Special HTTP headers that do not have the "HTTP_" prefix
     *
     * @var array
     */
    protected $specialHeaders = [
        'CONTENT_TYPE',
        'CONTENT_LENGTH',
        'CONTENT_MD5',
        'PHP_AUTH_USER',
        'PHP_AUTH_PW',
        'PHP_AUTH_DIGEST',
        'AUTH_TYPE'
    ];

    /**
     * Body
     *
     * @var StreamableInterface
     */
    protected $body;

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    /**
     * Create a new instance with the specified HTTP protocol version.
     *
     * @param string $version HTTP protocol version
     * @return self
     */
    public function withProtocolVersion($version)
    {
        if (!in_array($version, $this->validProtocols)) {
            throw new \InvalidArgumentException(
                'Unsupported HTTP protocol version; supported version are 1.0, 1.1 and 2.0'
            );
        }

        $clone = clone $this;
        $clone->protocol = $version;
        return $clone;
    }

    /**
     * Retrieves all message headers.
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings.
     */
    public function getHeaders()
    {
        return $this->headers;
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
            if ((!is_string($value) && !is_array($value)) || !is_string($key)) {
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

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return array_key_exists(strtolower($name), $this->headers);
    }

    /**
     * Retrieve a header by the given case-insensitive name, as a string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string
     */
    public function getHeader($name)
    {
        $header = $this->getHeaderLines($name);
        return ($header) ? implode(',', $header): '';
    }

    /**
     * Retrieves a header by the given case-insensitive name as an array of strings.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[]
     */
    public function getHeaderLines($name)
    {
        if (!$this->hasHeader($name)) {
            return [];
        }

        $header = $this->headers[strtolower($name)];
        return (is_array($header)) ? $header : [$header];
    }

    /**
     * Create a new instance with the provided header, replacing any existing
     * values of any headers with the same case-insensitive name.
     *
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value)
    {
        if (is_string($value)) {
            $value = [$value];
        }

        if (!$this->isValidHeader($name, $value)) {
            throw new \InvalidArgumentException(
                'Unsupported header value, must be a string or array of strings'
            );
        }

        $clone = clone $this;
        $clone->headers[strtolower($name)] = $value;
        return $clone;
    }

    /**
     * Creates a new instance, with the specified header appended with the
     * given value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value)
    {
        if (!$this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }

        if (is_string($value)) {
            $value = [$value];
        }

        if (!$this->isValidHeader($name, $value)) {
            throw new \InvalidArgumentException(
                'Unsupported header value, must be a string or array of strings'
            );
        }

        $name = strtolower($name);
        $clone = clone $this;
        $clone->headers[$name] = array_merge($this->headers[$name], $value);
        return $clone;
    }

    /**
     * Creates a new instance, without the specified header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name)
    {
        if (!$this->hasHeader($name)) {
            return $this;
        }

        $clone = clone $this;
        unset($clone->headers[strtolower($name)]);
        return $clone;
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamableInterface Returns the body as a stream.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Create a new instance, with the specified message body.
     *
     * @param StreamableInterface $body Body.
     * @return self
     * @throws \InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamableInterface $body)
    {
        $clone = clone $this;
        $clone->body = $body;
        return $clone;
    }

    /**
     * Check if a header is valid
     *
     * @param $name
     * @param $value
     * @return bool
     */
    private function isValidHeader($name, $value)
    {
        if (!is_string($name)) {
            return false;
        }

        if (is_string($value)) {
            return true;
        }

        if (is_array($value) && (array_filter($value, 'is_string') === $value)) {
            return true;
        }

        return false;
    }
}