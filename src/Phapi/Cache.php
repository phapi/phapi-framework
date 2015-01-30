<?php

namespace Phapi;

/**
 * Cache interface
 *
 * Interface for caching
 *
 * @category Cache
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
interface Cache
{

    /**
     * Connect to the cache server
     *
     * @return boolean
     */
    public function connect();

    /**
     * Set/add something to the cache
     *
     * @param  string $key Key for the value
     * @param  mixed  $value Value
     */
    public function set($key, $value);

    /**
     * Get something from the cache
     *
     * @param  string $key Key for the value
     */
    public function get($key);

    /**
     * Remove something from the cache
     *
     * @param  string $key Key for the value
     */
    public function clear($key);

    /**
     * Check if cache has a value for the key
     *
     * @param  string $key Key for the value
     */
    public function has($key);

    /**
     * Flush cache
     */
    public function flush();
}