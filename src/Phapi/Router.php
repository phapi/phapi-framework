<?php

namespace Phapi;

use Phapi\Exception\Error\MethodNotAllowed;
use Phapi\Exception\Error\NotFound;

/**
 * Router class
 *
 * A class that is responsible for finding matching routes and resources to the
 * supplied request uri and method.
 *
 * @category Router
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Router {

    /**
     * Routes
     *
     * @var array
     */
    protected $routes;

    /**
     * Matched route
     *
     * @var string|null
     */
    protected $matchedRoute;

    /**
     * Matched Resource
     *
     * @var string|null
     */
    protected $matchedResource;

    /**
     * Matched method
     *
     * @var string|null
     */
    protected $matchedMethod;

    /**
     * Params
     *
     * @var array
     */
    protected $params = [];

    /**
     * Cache
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Route parser
     *
     * @var RouteParser
     */
    protected $routeParser;

    /**
     * The provided requested URI
     *
     * @var string
     */
    protected $requestUri;

    /**
     * The provided request method
     *
     * @var string
     */
    protected $requestMethod;

    /**
     * Constructor
     *
     * @param RouteParser $parser
     * @param array $routes
     */
    public function __construct(RouteParser $parser, array $routes = [])
    {
        // Set routes
        $this->setRoutes($routes);

        // Set parser
        $this->routeParser = $parser;
    }

    /**
     * Takes the request uri and matches it against all defined routes.
     * If a route can be found the function looks if the request method
     * has a matching function defined in the resource.
     *
     * Params from the uri are also extracted.
     *
     * @param $requestUri
     * @param $requestMethod
     * @return bool
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    public function match($requestUri, $requestMethod)
    {
        // remove query string
        $this->requestUri = strtok($requestUri, '?');

        $this->requestMethod = $requestMethod;


        // start matching routes

        // look for a direct match in the routes table
        if ($this->matchDirect()) {
            // match found, no need to look for more
            return true;
        } elseif ($this->matchCache()) {
            return true;
        } elseif ($this->matchRegex()) {
            return true;
        }

        // could not find a matching route
        throw new NotFound();
    }

    /**
     * Check if we can find a direct match (without regex) in the
     * route table.
     *
     * @return bool
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    protected function matchDirect()
    {
        // Create variable used to keep track on progress
        $resource = false;

        // check without a trailing slash
        if (isset($this->routes[rtrim($this->requestUri, '/')])) {
            $this->requestUri = rtrim($this->requestUri, '/');
            $resource = $this->routes[$this->requestUri];
        } elseif (isset($this->routes[rtrim($this->requestUri, '/') . '/'])) {
            // check with a trailing slash
            $this->requestUri = rtrim($this->requestUri, '/') . '/';
            $resource = $this->routes[$this->requestUri];
        }

        // Check if resource is something useful
        if ($resource === false) {
            return false;
        }

        // check if resource class exists
        if (!$this->resourceExists($resource)) {
            throw new NotFound();
        }

        if (!$this->resourceMethodExists($resource, $this->requestMethod)) {
            // route found but method can't be found
            throw new MethodNotAllowed();
        }

        // found a direct match, set needed properties
        $this->matchedRoute = $this->requestUri;
        $this->matchedResource = $resource;
        $this->matchedMethod = $this->requestMethod;

        // we found our match, stop looking for more
        return true;
    }

    /**
     * Check if we can find the requested route in cache
     *
     * @return bool
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    protected function matchCache()
    {
        // make sure we have a trailing slash
        $requestUri = rtrim($this->requestUri, '/') . '/';

        // check if we can find a match in cache
        // check if a cache exists
        if ($this->cache instanceof Cache) {
            // check if we have any routes saved in cache
            if ($cachedRoutes = $this->cache->get('routes')) {
                // check if the requested uri can be found in the cache
                if (isset($cachedRoutes[$requestUri])) {

                    // check if resource class exists
                    if (!$this->resourceExists($cachedRoutes[$requestUri]['matchedResource'])) {
                        throw new NotFound();
                    }

                    if (!$this->resourceMethodExists($cachedRoutes[$requestUri]['matchedResource'], $this->requestMethod)) {
                        // route found but method can't be found
                        throw new MethodNotAllowed();
                    }

                    // match found, set info
                    $this->matchedRoute = $cachedRoutes[$requestUri]['matchedRoute'];
                    $this->matchedResource = $cachedRoutes[$requestUri]['matchedResource'];
                    $this->matchedMethod  = $this->requestMethod;
                    $this->params  = $cachedRoutes[$requestUri]['params'];

                    // no need to look for more
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Try to match against regex, this is the last resort, usually we first
     * try to find a direct match and look in the cache as well. But if no
     * match has been found yet this is the last try.
     *
     * @return bool
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    protected function matchRegex()
    {
        // make sure we have a trailing slash
        $requestUri = rtrim($this->requestUri, '/') . '/';

        // match request against route table
        foreach ($this->routes as $route => $resource) {
            $result = $this->routeParser->parse($route);

            // get regex
            $regex = $result[0];
            // get param names
            $paramNames = $result[1];

            if (preg_match($regex, $requestUri, $matches)) {
                // check if resource class exists
                if (!$this->resourceExists($resource)) {
                    // resource class not found
                    throw new NotFound();
                }

                // check if method exists
                if (!$this->resourceMethodExists($resource, $this->requestMethod)) {
                    // route found but method can't be found
                    throw new MethodNotAllowed();
                }

                // set matched route
                $this->matchedRoute = $route;
                // set matched resource
                $this->matchedResource = $resource;
                // set matched request method
                $this->matchedMethod = $this->requestMethod;

                // remove all slashes from param values/matches
                $matches = array_diff($matches, ['/']);

                // remove first value in the matches array since it wont be used
                array_shift($matches);

                // loop through all params and match them with values and save the result
                // to the params array
                foreach ($paramNames as $key => $name) {
                    $this->params[$name] = (isset($matches[$key])) ? $matches[$key]: null;
                }

                // we found our match, stop looking for more and add to cache if cache is configured
                if ($this->cache instanceof Cache) {
                    // prepare info to be saved to cache
                    $toCache = [
                        'matchedRoute' => $this->matchedRoute,
                        'matchedResource' => $this->matchedResource,
                        'params' => $this->params
                    ];

                    // check if we have any routes saved
                    if ($cachedRoutes = $this->cache->get('routes')) {
                        // add this route to cache
                        $cachedRoutes[$requestUri] = $toCache;
                    } else {
                        // no routes found in cache, this is the first one we save
                        $cachedRoutes = [
                            $requestUri => $toCache
                        ];
                    }
                    // save to cache
                    $this->cache->set('routes', $cachedRoutes);
                }

                // time to stop looking for more matches
                return true;
            }
        }

        return false;
    }
    /**
     * Make sure the resource class exists
     *
     * @param $resource
     * @return bool
     */
    protected function resourceExists($resource)
    {
        return class_exists($resource);
    }

    /**
     * Make sure resource method exists
     *
     * @param $resource
     * @param $method
     * @return bool
     */
    protected function resourceMethodExists($resource, $method)
    {
        return method_exists($resource, $method);
    }

    /**
     * Set routes
     *
     * @param array $routes
     */
    public function setRoutes($routes)
    {
        $this->routes = [];
        $this->addRoutes($routes);
    }

    /**
     * Add a route to the route table
     *
     * @param string $route    Route to add to the route table
     * @param string $resource Resource that the route points to
     */
    public function addRoute($route, $resource)
    {
        $this->routes[$route] = $resource;
    }

    /**
     * Add multiple routes to the router table as the same time
     *
     * @param array $routes Add multiple routes at the same time
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $route => $resource) {
            $this->addRoute($route, $resource);
        }
    }

    /**
     * Get all routes added to the route table
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get the matched method
     *
     * @return null|string
     */
    public function getMatchedMethod()
    {
        return $this->matchedMethod;
    }

    /**
     * Get the matched resource
     *
     * @return null|string
     */
    public function getMatchedResource()
    {
        return $this->matchedResource;
    }

    /**
     * Get the matched route
     *
     * @return null|string
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set cache storage
     *
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }
}