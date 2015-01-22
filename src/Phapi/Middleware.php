<?php

namespace Phapi;

/**
 * Abstract middleware class
 *
 * Abstract class for middlewares. Implements basic
 * functionality that all middlewares needs.
 *
 * @abstract
 * @category Middleware
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
abstract class Middleware
{

    /**
     * Reference to the primary application instance
     *
     * @var Phapi
     */
    protected $app;

    /**
     * Reference to the next downstream middleware
     *
     * @var Phapi|Middleware
     */
    protected $next;

    /**
     * Set application
     *
     * This method injects the primary Slim application instance into
     * this middleware.
     *
     * @param Phapi $app
     */
    final public function setApplication(Phapi $app)
    {
        $this->app = $app;
    }

    /**
     * Get application
     *
     * This method retrieves the application previously injected
     * into this middleware.
     *
     * @return Phapi
     */
    final public function getApplication()
    {
        return $this->app;
    }

    /**
     * Set next middleware
     *
     * This method injects the next downstream middleware into
     * this middleware so that it may optionally be called
     * when appropriate.
     *
     * @param $next Phapi|Middleware
     */
    final public function setNextMiddleware($next)
    {
        $this->next = $next;
    }

    /**
     * Get next middleware
     *
     * This method retrieves the next downstream middleware
     * previously injected into this middleware.
     *
     * @return Middleware|Phapi
     */
    final public function getNextMiddleware()
    {
        return $this->next;
    }

    /**
     * Call
     *
     * Perform actions specific to this middleware and optionally
     * call the next downstream middleware.
     *
     * @abstract
     */
    abstract public function call();
}