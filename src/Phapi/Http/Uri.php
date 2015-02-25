<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Http;

use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 *
 * @category Phapi
 * @package  Phapi\Http
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Uri implements UriInterface {

    /**
     * @var string
     */
    private $scheme = '';
    /**
     * @var string
     */
    private $userInfo = '';
    /**
     * @var string
     */
    private $host = '';
    /**
     * @var int
     */
    private $port = null;
    /**
     * @var string
     */
    private $path = '';
    /**
     * @var string
     */
    private $query = '';
    /**
     * @var string
     */
    private $fragment = '';

    public function __construct($uri = '')
    {
        // Check if URI is string
        if (!is_string($uri)) {
            throw new \InvalidArgumentException('URI passed to constructor must be a string');
        }

        // Parse URI
        if (!empty($uri)) {
            $this->parse($uri);
        }
    }

    /**
     * Retrieve the URI scheme.
     *
     * @return string The scheme of the URI.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retrieve the authority portion of the URI. The authority portion of the
     * URI is [user-info@]host[:port]
     *
     * @return string Authority portion of the URI, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        // Check if we have the only required part of the string: the host
        if (empty($this->host)) {
            return '';
        }

        // Add host to the authority string
        $authority = $this->host;

        // Check if we have the userInfo
        if (!empty($this->userInfo)) {
            $authority = $this->userInfo .'@'. $authority;
        }

        // Check if the port should be added
        if (!$this->isStandardPort($this->scheme, $this->port)) {
            $authority .= ':'. $this->port;
        }

        return $authority;
    }

    /**
     * Retrieve the user information portion of the URI, if present.
     *
     * @return string User information portion of the URI, if present, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * Retrieve the host segment of the URI.
     *
     * @return string Host segment of the URI.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retrieve the port segment of the URI.
     *
     * @return null|int The port for the URI.
     */
    public function getPort()
    {
        return ($this->isStandardPort($this->scheme, $this->port)) ? null : $this->port;
    }

    /**
     * Retrieve the path segment of the URI.
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
        return (!empty($this->path)) ? $this->path : '/';
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment segment of the URI.
     *
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Create a new instance with the specified scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return self A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        // To lowercase
        $scheme = strtolower($scheme);

        // Remove :// if it exists
        if (strpos($scheme, '://')) {
            $scheme = str_replace('://', '', $scheme);
        }

        // Make sure specified scheme is supported
        if (!in_array($scheme, ['', 'http', 'https'], true)) {
            throw new \InvalidArgumentException(
                'Unsupported scheme, must be one of: an empty string, "http", or "https"'
            );
        }

        $new = clone($this);
        $new->scheme = $scheme;
        return $new;
    }

    /**
     * Create a new instance with the specified user information.
     *
     * @param string $user User name to use for authority.
     * @param null|string $password Password associated with $user.
     * @return self A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $userInfo = $user;
        if ($password !== null) {
            $userInfo .= ':'. $password;
        }

        $new = clone($this);
        $new->userInfo = $userInfo;
        return $new;
    }

    /**
     * Create a new instance with the specified host.
     *
     * @param string $host Hostname to use with the new instance.
     * @return self A new instance with the specified host.
     */
    public function withHost($host)
    {
        $new = clone($this);
        $new->host = $host;
        return $new;
    }

    /**
     * Create a new instance with the specified port.
     *
     * @param null|int $port Port to use with the new instance; a null value
     *     removes the port information.
     * @return self A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        // Make sure specified port is an integer or can be converted to one
        if (!is_integer($port) || (is_string($port) && !is_numeric($port))) {
            throw new \InvalidArgumentException(
                'Invalid port specified, must be an integer or integer string'
            );
        }

        // Convert to int
        $port = (int) $port;

        // Check if port is a valid port
        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException(
                'Invalid port specified, must be a valid TCP/UDP port'
            );
        }

        $new = clone($this);
        $new->port = $port;
        return $new;
    }

    /**
     * Create a new instance with the specified path.
     *
     * @param string $path The path to use with the new instance.
     * @return self A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        // Make sure path is a string
        if (!is_string($path)) {
            throw new \InvalidArgumentException(
                'Invalid path provided, must be a string'
            );
        }

        // Make sure it doesn't contain a query string
        if (strpos($path, '?') !== false) {
            throw new \InvalidArgumentException(
                'Invalid path provided, must not contain a query string'
            );
        }

        // Make sure it doesn't contain fragment
        if (strpos($path, '#') !== false) {
            throw new \InvalidArgumentException(
                'Invalid path provided, must not contain a URI fragment'
            );
        }

        // Fix prefixed "/"
        if (!empty($path) && strpos($path, '/') !== 0) {
            $path = '/'. $path;
        }

        $new = clone($this);
        $new->path = $path;
        return $new;
    }

    /**
     * Create a new instance with the specified query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return self A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        // Make sure its a string
        if (! is_string($query)) {
            throw new \InvalidArgumentException(
                'Query string must be a string'
            );
        }

        // Check for URI fragments
        if (strpos($query, '#') !== false) {
            throw new \InvalidArgumentException(
                'Query string must not include a URI fragment'
            );
        }

        // Remove any prefixed "?"
        if (strpos($query, '?') === 0) {
            $query = substr($query, 1);
        }

        $new = clone $this;
        $new->query = $query;
        return $new;
    }

    /**
     * Create a new instance with the specified URI fragment.
     *
     * @param string $fragment The URI fragment to use with the new instance.
     * @return self A new instance with the specified URI fragment.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withFragment($fragment)
    {
        // Check if string
        if (!is_string($fragment)) {
            throw new \InvalidArgumentException(
                'Fragment must be a string'
            );
        }

        // Remove prefixed "#"
        if (strpos($fragment, '#') === 0) {
            $fragment = substr($fragment, 1);
        }

        $new = clone $this;
        $new->fragment = $fragment;
        return $new;
    }

    /**
     * Return the string representation of the URI.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->createUriString();
    }

    /**
     * Parse the uri and set the parameters
     *
     * @param $uri
     */
    private function parse($uri)
    {
        $parts = parse_url($uri);

        $this->scheme   = $this->parseScheme($parts);
        $this->userInfo = $this->parseUserInfo($parts);
        $this->host     = $this->parseHost($parts);
        $this->port     = $this->parsePort($parts);
        $this->path     = $this->parsePath($parts);
        $this->query    = $this->parseQuery($parts);
        $this->fragment = $this->parseFragment($parts);
    }

    /**
     * Find an return scheme parameter
     *
     * @param array $parts
     * @return string
     */
    private function parseScheme($parts)
    {
        return (isset($parts['scheme'])) ? $parts['scheme'] : ''; // e.g. http
    }

    /**
     * Find and build userInfo parameter
     *
     * @param array $parts
     * @return string
     */
    private function parseUserInfo($parts)
    {
        return (
            isset($parts['user'])
        ) ?
            (isset($parts['pass'])) ? $parts['user'] .':'. $parts['pass'] : $parts['user'] :
            '';
    }

    /**
     * Find an return host parameter
     *
     * @param array $parts
     * @return string
     */
    private function parseHost($parts)
    {
        return (isset($parts['host'])) ? $parts['host'] : '';
    }

    /**
     * Find an return port parameter
     *
     * @param array $parts
     * @return string
     */
    private function parsePort($parts)
    {
        return (isset($parts['port'])) ? $parts['port'] : null;
    }

    /**
     * Find an return path parameter
     *
     * @param array $parts
     * @return string
     */
    private function parsePath($parts)
    {
        return (isset($parts['path'])) ? $parts['path'] : '';
    }

    /**
     * Find an return query string parameter
     *
     * @param array $parts
     * @return string
     */
    private function parseQuery($parts)
    {
        return (isset($parts['query'])) ? $parts['query'] : '';
    }

    /**
     * Find an return fragment parameter
     *
     * @param array $parts
     * @return string
     */
    private function parseFragment($parts)
    {
        return (isset($parts['fragment'])) ? $parts['fragment'] : '';
    }

    /**
     * Determine if the port is the standard port or not
     *
     * @param $scheme
     * @param $port
     * @return bool
     */
    private function isStandardPort($scheme, $port)
    {
        if (!$scheme) {
            // If we don't know the scheme then we don't know if it's
            // the standard port so we return true as default.
            return true;
        }

        if (!$port) {
            // We don't know the port so we cant determine if it's
            // the standard port so we return true as default
            return true;
        }

        if ($scheme == 'https' && $port === 443) {
            return true;
        }

        if ($scheme == 'http' && $port === 80) {
            return true;
        }

        return false;
    }

    /**
     * Create a URI string from its various parts
     *
     * @return string
     */
    private function createUriString()
    {
        $uri = '';

        if (!empty($this->scheme)) {
            $uri .= sprintf('%s://', $this->scheme);
        }

        if (!empty($authority = $this->getAuthority())) {
            $uri .= $authority;
        }

        if ($path = $this->getPath()) {
            $uri .= $path;
        }

        if ($query = $this->getQuery()) {
            $uri .= sprintf('?%s', $query);
        }

        if ($fragment = $this->getFragment()) {
            $uri .= sprintf('#%s', $fragment);
        }

        return $uri;
    }
}