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
     * Returns "http" or "https", unless another scheme is used.
     * If no scheme is present an empty string is returned.
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
     * If the port component is not set or is the standard port for the current
     * scheme, it will not be included.
     *
     * This method will return an empty string if no authority information is
     * present.
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
     * If a user is present in the URI, this will return that value; additionally,
     * if the password is also present, it will be appended to the user value, with
     * a colon (":") separating the values.
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
     * This method will return a string; if no host segment is present, an
     * empty string MUST be returned.
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
     * If no port is present, but a scheme is present, this method will return null.
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
     * This method will return a string; if no path is present it will return
     * an empty string.
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
     * This method will return a string; if no query string is present, it will
     * return an empty string.
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
     * This method returns a string; if no fragment is present, it will return an
     * empty string. The string returned will omit the leading "#" character.
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified scheme. If the scheme
     * provided includes the "://" delimiter, it will be removed.
     *
     * Implementations will restrict values to "http", "https", or an empty string.
     *
     * An empty scheme is equivalent to removing the scheme.
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified user information.
     *
     * Password is optional, but the user information MUST include the
     * user; an empty string for the user is equivalent to removing user
     * information.
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified host.
     *
     * An empty host value is equivalent to removing the host.
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified port.
     *
     * Implementations will raise an exception for ports outside the
     * established TCP and UDP port ranges.
     *
     * A null value provided for the port is equivalent to removing the port
     * information.
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified path.
     *
     * The path may be prefixed with "/"; if not, the implementation will
     * provide the prefix itself.
     *
     * An empty path value is equivalent to removing the path.
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

        // Make sure it doesn't contain query string
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified query string.
     *
     * If the query string is prefixed by "?", that character will be removed.
     *
     * An empty query string value is equivalent to removing the query string.
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
     * This method will retain the state of the current instance, and return
     * a new instance that contains the specified URI fragment.
     *
     * If the fragment is prefixed by "#", that character will be removed.
     *
     * An empty fragment value is equivalent to removing the fragment.
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
     * Concatenates the various segments of the URI, using the appropriate
     * delimiters:
     *
     * - If a scheme is present, "://" MUST append the value.
     * - If the authority information is present, that value will be
     *   concatenated.
     * - If a path is present, it MUST be prefixed by a "/" character.
     * - If a query string is present, it MUST be prefixed by a "?" character.
     * - If a URI fragment is present, it MUST be prefixed by a "#" character.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->createUriString(
            $this->getScheme(),
            $this->getAuthority(),
            $this->getPath(),
            $this->getQuery(),
            $this->getFragment()
        );
    }

    /**
     * Parse the uri and set the parameters
     *
     * @param $uri
     */
    private function parse($uri)
    {
        $parts = parse_url($uri);

        $this->scheme    = (isset($parts['scheme']))     ? $parts['scheme']     : ''; // e.g. http
        $this->host      = (isset($parts['host']))       ? $parts['host']       : '';
        $this->port      = (isset($parts['port']))       ? $parts['port']       : null;
        $this->userInfo  = (isset($parts['user']))       ? $parts['user']       : '';
        $this->userInfo .= (isset($parts['pass']))       ? ':'. $parts['pass']  : '';
        $this->path      = (isset($parts['path']))       ? $parts['path']       : '';
        $this->query     = (isset($parts['query']))      ? $parts['query']      : ''; // after the question mark ?
        $this->fragment  = (isset($parts['fragment']))   ? $parts['fragment']   : ''; // after the hashmark #
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
        // Check if we know the scheme
        if (!$scheme) {
            // If we don't know the scheme then we don't know if it's
            // the standard port so we return true as default.
            return true;
        }

        // Check if we know the port
        if (!$port) {
            // We don't know the port so we cant determine if it's
            // the standard port so we return true as default
            return true;
        }

        // Check if scheme is https and if port is 443
        if ($scheme == 'https' && $port === 443) {
            // It's https but not 443
            return true;
        }

        // Check if scheme is http and if port is 80
        if ($scheme == 'http' && $port === 80) {
            // It's http but not 80
            return true;
        }

        // Return false as default
        return false;
    }

    /**
     * Create a URI string from its various parts
     *
     * @param string $scheme
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     * @return string
     */
    private static function createUriString($scheme, $authority, $path, $query, $fragment)
    {
        $uri = '';
        if (!empty($scheme)) {
            $uri .= sprintf('%s://', $scheme);
        }
        if (! empty($authority)) {
            $uri .= $authority;
        }
        if ($path) {
            $uri .= $path;
        }
        if ($query) {
            $uri .= sprintf('?%s', $query);
        }
        if ($fragment) {
            $uri .= sprintf('#%s', $fragment);
        }
        return $uri;
    }
}