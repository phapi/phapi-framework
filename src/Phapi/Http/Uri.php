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
 * Provides a value object representing a URI for HTTP requests.
 *
 * This class is considered immutable; all methods that might change state are implemented
 * such that they retain the internal state of the current instance and return a new instance
 * that contains the changed state.
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
    private $user = '';

    /**
     * @var null|string
     */
    private $pass = null;

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var int
     */
    private $port;

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

    /**
     * @param string $uri
     * @throws \InvalidArgumentException on non-string $uri argument
     */
    public function __construct($uri = '')
    {
        if (! is_string($uri)) {
            throw new \InvalidArgumentException(
                'URI must be a string'
            );
        }

        $this->parse($uri);
    }

    /**
     * Return the string representation of the URI.
     * Concatenates the various segments of the URI, using the appropriate delimiters
     *
     * @return string
     */
    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        return
            ($scheme ? $scheme . '://' : '') .
            $authority .
            $path .
            ($query ? '?' . $query : '') .
            ($fragment ? '#' . $fragment : '');
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
     * Retrieve the authority portion of the URI.
     *
     * The authority portion of the URI is [user-info@]host[:port]
     *
     * @return string Authority portion of the URI, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $authority = $this->host;

        $port = $this->getPort();
        if ($port) {
            $authority .= ':'. $port;
        }

        $userInfo = $this->getUserInfo();
        if ($userInfo) {
            $authority = $userInfo .'@' . $authority;
        }

        return $authority;
    }

    /**
     * Retrieve the user information portion of the URI, if present.
     *
     * @return string User information portion of the URI, if present, in
     *     "username[:password]" format.
     */
    public function getUserInfo()
    {
        return (!is_null($this->pass)) ? $this->user. ':'. $this->pass : $this->user;
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
     * If a port is present, and it is non-standard for the current scheme,
     * this method will return it as an integer. If the port is the standard port
     * used with the current scheme, this method will return null.
     *
     * If no port is present, and no scheme is present, this method will return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The port for the URI.
     */
    public function getPort()
    {
        if (
            is_integer($this->port) &&
            (
                ($this->scheme === 'http' && $this->port !== 80) ||
                ($this->scheme === 'https' && $this->port !== 443)
            )
        ) {
            return $this->port;
        }

        return null;
    }

    /**
     * Retrieve the path segment of the URI.
     *
     * This method MUST return a string; if no path is present it MUST return
     * an empty string.
     *
     * If the path is empty, this method MUST return "/".
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
        return (empty($this->path)) ? '/' : $this->path;
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
        if (!is_string($scheme)) {
            throw new \InvalidArgumentException(
                'Unsupported scheme; must be "", "http" or "https"'
            );
        }

        $scheme = str_replace('://', '', $scheme);

        if (!in_array($scheme, ['', 'http', 'https'])) {
            throw new \InvalidArgumentException(
                'Unsupported scheme; must be "", "http" or "https"'
            );
        }

        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }
    /**
     * Create a new instance with the specified user information.
     *
     * @param string $user User name to use for authority.
     * @param null|string $password Password associated with $user.
     * @return self A new instance with the specified user information.
     * @throws \InvalidArgumentException for invalid or unsupported user or password.
     */
    public function withUserInfo($user, $password = null)
    {
        if (!is_string($user) || (!is_null($password) && !is_string($password))) {
            throw new \InvalidArgumentException(
                'Unsupported user or password, must be string'
            );
        }

        $clone = clone $this;
        $clone->user = $user;
        $clone->pass = $password;
        return $clone;
    }

    /**
     * Create a new instance with the specified host.
     *
     * @param string $host Hostname to use with the new instance.
     * @return self A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid or unsupported host.
     */
    public function withHost($host)
    {
        if (!is_string($host)) {
            throw new \InvalidArgumentException(
                'Unsupported host, must be string'
            );
        }

        $clone = clone $this;
        $clone->host = $host;
        return $clone;
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
        if (is_null($port) ||
            (is_integer($port) &&
                $port >= 1 &&
                $port <= 65535
            )
        ) {
            $clone = clone $this;
            $clone->port = $port;
            return $clone;
        }

        throw new \InvalidArgumentException(
            'Unsupported port provided; must be null or an integer within 1 to 65535'
        );
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
        if (!is_string($path)) {
            throw new \InvalidArgumentException(
                'Unsupported path, must be a string'
            );
        }

        if (strpos($path, '?')) {
            throw new \InvalidArgumentException(
                'Unsupported path, must not contain a query string'
            );
        }

        if (strpos($path, '#')) {
            throw new \InvalidArgumentException(
                'Unsupported path, must not contain a URI fragment'
            );
        }

        if (! empty($path) && strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }

        $clone = clone $this;
        $clone->path = $path;
        return $clone;
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
        if (! is_string($query)) {
            throw new \InvalidArgumentException(
                'Unsupported Query string; must be a string'
            );
        }

        if (strpos($query, '#') !== false) {
            throw new \InvalidArgumentException(
                'Unsupported Query string; must not include a URI fragment'
            );
        }

        if (strpos($query, '?') === 0) {
            $query = substr($query, 1);
        }

        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    /**
     * Create a new instance with the specified URI fragment.
     *
     * @param string $fragment The URI fragment to use with the new instance.
     * @return self A new instance with the specified URI fragment.
     */
    public function withFragment($fragment)
    {
        if (! is_string($fragment)) {
            throw new \InvalidArgumentException(
                'Unsupported fragment; must be a string'
            );
        }

        if (strpos($fragment, '#') === 0) {
            $fragment = substr($fragment, 1);
        }

        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    /**
     * Parse the uri and save as parameters
     *
     * @param $uri
     */
    protected function parse($uri)
    {
        $parts = parse_url($uri);

        foreach ($parts as $key => $value) {
            $this->$key = $value;
        }
    }
}