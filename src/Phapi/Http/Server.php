<?php

namespace Phapi\Http;

use Phapi\Bucket;

/**
 * Server class
 *
 * Class/object representing server parameters
 *
 * @category Http
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Server extends Bucket
{

    /**
     * Gets the HTTP headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        $contentHeaders = ['CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true];
        foreach ($this->storage as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } elseif (isset($contentHeaders[$key])) { // CONTENT_* are not prefixed with HTTP_
                $headers[$key] = $value;
            }
        }

        if (isset($this->parameters['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $this->storage['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = isset($this->storage['PHP_AUTH_PW']) ? $this->storage['PHP_AUTH_PW'] : '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */

            $authorizationHeader = null;
            if (isset($this->storage['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->storage['HTTP_AUTHORIZATION'];
            } elseif (isset($this->storage['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $this->storage['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)));
                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }
                } elseif (
                    empty($this->parameters['PHP_AUTH_DIGEST']) &&
                    (0 === stripos($authorizationHeader, 'digest'))
                ) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $this->parameters['PHP_AUTH_DIGEST'] = $authorizationHeader;
                }
            }
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic '.base64_encode($headers['PHP_AUTH_USER'].':'.$headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        return $headers;
    }
}