<?php

namespace Phapi\Middleware;

use Phapi\Exception\Error\BadRequest;
use Phapi\Middleware;

/**
 * Middleware class
 *
 * Middleware class for handling CORS requests.
 *
 * See https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 * for more information about CORS and how it works
 *
 * @category Middleware
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Cors extends Middleware {

    /**
     * Options / settings / configuration
     *
     * @var array
     */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = $this->prepareOptions($options);
    }

    public function call()
    {
        // Check if origin header is set, if it isn't then this isn't a CORS compatible request
        if (!$this->app->getRequest()->getHeaders()->has('origin')) {
            // Call next middleware
            if ($this->next !== null) {
                $this->next->call();
            }
            return;
        }

        // Check if it is an OPTIONS request
        if ($this->app->getRequest()->isOptions()) {
            // It's an OPTIONS request

            // Check if Origin header is set and origin is allowed
            if (!$this->checkOrigin()) {
                // The provided origin is not allowed
                throw new BadRequest('Origin not allowed');
            }

            // All checks passed so lets set some headers
            $this->createPreflightHeaders();

        } else {
            // Not an OPTIONS request

            // Check if it is a CORS request (check for Origin header) and if the
            // Origin header value matches an allowed origin.
            if (!$this->checkOrigin()) {
                throw new BadRequest('Origin not allowed according to CORS');
            }

            // Check if the request method is allowed
            if (!$this->checkMethod()) {
                throw new BadRequest('Method not allowed according to CORS');
            }

            // All checks passed so lets set some headers
            $this->createRequestHeaders();
        }

        // Call next middleware
        if ($this->next !== null) {
            $this->next->call();
        }
    }

    /**
     * Create all headers for a preflight request
     *
     * @throws BadRequest
     */
    protected function createPreflightHeaders()
    {
        $headers = [];

        // Set Allowed origin header (either * or Origin from request)
        $headers = $this->createOriginHeader($headers);

        // Set support credentials header
        $headers = $this->createCredentialsHeader($headers);

        // Set exposed headers header
        $headers = $this->createExposedHeadersHeader($headers);

        // Set allowed methods header
        $headers = $this->createAllowedMethodsHeader($headers);

        // Set allowed headers header
        $headers = $this->createAllowedHeadersHeader($headers);

        // Set max age header
        $headers = $this->createMaxAgeHeader($headers);

        // Add the headers to the response
        $this->app->getResponse()->addHeaders($headers);
    }

    /**
     * Create all headers for a regular request
     */
    protected function createRequestHeaders()
    {
        $headers = [];

        // Set Allowed origin header (either * or Origin from request)
        $headers = $this->createOriginHeader($headers);

        // Set support credentials header
        $headers = $this->createCredentialsHeader($headers);

        // Set exposed headers header
        $headers = $this->createExposedHeadersHeader($headers);

        // Add the headers to the response
        $this->app->getResponse()->addHeaders($headers);
    }

    /**
     * Set Allowed origin header (either * or Origin from request)
     *
     * @param $headers
     * @return array|string
     */
    protected function createOriginHeader($headers)
    {
        // Check if all origins are allowed
        if ($this->options['allowedOrigins'] === true) {
            // All origins are allowed
            $headers['Access-Control-Allow-Origin'] = '*';
        } else {
            // Set the value of the request header
            $headers['Access-Control-Allow-Origin'] = $this->app->getRequest()->getHeaders()->get('origin');

            // Set the Vary header (needs to be done according to the specification)
            $headers = $this->createVaryHeader($headers);
        }

        return $headers;
    }

    /**
     * Check if a allowed credentials header should be created
     * and create it with proper value if so
     *
     * @param $headers
     * @return mixed
     */
    protected function createCredentialsHeader($headers)
    {
        if ($this->options['supportsCredentials'] === true) {
            $headers['Access-Control-Allow-Credentials'] = 'true';
        }

        return $headers;
    }

    /**
     * Check if the exposed headers header should be created
     * and add the proper value to the header if it should
     * be created
     *
     * @param $headers
     * @return mixed
     */
    protected function createExposedHeadersHeader($headers)
    {
        if (count($this->options['exposedHeaders']) > 0) {
            $headers['Access-Control-Expose-Headers'] = implode(', ', $this->options['exposedHeaders']);
        }
        return $headers;
    }

    /**
     * Create the allowed methods header
     *
     * @param $headers
     * @return mixed
     * @throws BadRequest
     */
    protected function createAllowedMethodsHeader($headers)
    {
        // Make sure that the client provided access control request method header
        if (!$this->app->getRequest()->getHeaders()->has('Access-Control-Request-Method')) {
            throw new BadRequest('The Access-Control-Request-Method is missing from the request');
        }

        $headers['Access-Control-Allow-Methods'] = ($this->options['allowedMethods'] === true)
            ? strtoupper($this->app->getRequest()->getHeaders()->get('Access-Control-Request-Method'))
            : implode(', ', $this->options['allowedMethods']);
        return $headers;
    }

    /**
     * Create the allowed headers header if needed
     *
     * @param $headers
     * @return mixed
     */
    protected function createAllowedHeadersHeader($headers)
    {
        // Check if the client provided an access control request headers header.
        // If the header isn't set, then there is no need to include the header in the response
        if ($this->app->getRequest()->getHeaders()->has('Access-Control-Request-Headers')) {
            $headers['Access-Control-Allow-Headers'] = ($this->options['allowedHeaders'] === true)
                ? strtoupper($this->app->getRequest()->getHeaders()->get('Access-Control-Request-Headers'))
                : implode(', ', $this->options['allowedHeaders']);
        }

        return $headers;
    }

    /**
     * Create the max age header
     *
     * @param $headers
     * @return mixed
     */
    protected function createMaxAgeHeader($headers)
    {
        $headers['Access-Control-Max-Age'] = $this->options['maxAge'];

        return $headers;
    }

    /**
     * Create/update the vary header
     *
     * @param $headers
     * @return mixed
     */
    protected function createVaryHeader($headers)
    {
        // Check if the header already exists
        if (!$this->app->getResponse()->getHeaders()->has('Vary')) {
            $headers['Vary'] = 'Origin';
        } else {
            $headers['Vary'] = $this->app->getResponse()->getHeaders()->get('Vary') . ', Origin';
        }

        return $headers;
    }

    /**
     * Check if the provided origin is allowed to
     * access the api
     *
     * @return bool
     */
    protected function checkOrigin()
    {
        // Check if we allow all "*"
        if ($this->options['allowedOrigins'] === true) {
            return true;
        }

        // Make sure the origin header is set and that the value (domain) is
        // in the allowed origins list.
        if (
            $this->app->getRequest()->getHeaders()->has('origin') &&
            in_array($this->app->getRequest()->getHeaders()->get('origin'), $this->options['allowedOrigins'])
        ) {
            return true;
        }

        return false;
    }

    /**
     * Make sure that the current request method is allowed to
     * be done to the api.
     *
     * @return bool
     */
    protected function checkMethod()
    {

        // Check if we allow all "*"
        if ($this->options['allowedMethods'] === true) {
            return true;
        }

        // Check if the current request method is allowed according to the configuration
        if (in_array($this->app->getRequest()->getMethod(), $this->options['allowedMethods'])) {
            return true;
        }

        return false;
    }

    /**
     * Prepare options by adding defaults and merging them with the provided
     * options. After that, check if we allow all origins, headers and options
     * and do some normalizing at the same time like fixing lowercase and UPPERCASE.
     *
     * @param $options
     * @return array
     */
    protected function prepareOptions($options)
    {
        $defaults = [
            'allowedOrigins' => [],
            'allowedMethods' => [],
            'allowedHeaders' => [],
            'exposedHeaders' => [],
            'maxAge' => 0,
            'supportsCredentials' => false,
        ];
        $options = array_merge($defaults, $options);

        // Check if we allow all origins
        if (in_array('*', $options['allowedOrigins'])) {
            $options['allowedOrigins'] = true;
        }

        // Check if we allow all headers
        if (in_array('*', $options['allowedHeaders'])) {
            $options['allowedHeaders'] = true;
        } else {
            // Normalize all set allowed headers by making them lowercase
            $options['allowedHeaders'] = array_map('strtolower', $options['allowedHeaders']);
        }

        // Check if we allow all methods
        if (in_array('*', $options['allowedMethods'])) {
            $options['allowedMethods'] = true;
        } else {
            // Normalize all allowed methods by making them UPPERCASE
            $options['allowedMethods'] = array_map('strtoupper', $options['allowedMethods']);
        }

        return $options;
    }
}