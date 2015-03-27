<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Contract;

/**
 * Interface Container
 *
 * An interface for the dependency injection container
 * used by Phapi.
 *
 * @category Phapi
 * @package  Phapi\Contract
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
interface Cache {

    /**
     * Connect to the cache server
     *
     * If we are unable to connect to the cache backend an Exception should be
     * thrown. That Exception should be handled by the method calling this connect
     * method.
     *
     * A working cache is NOT a requirement for the application to run so it's
     * important to handle the exception and let the application run.
     * Suggestion: if the exception below is thrown a new NullCache should be created
     *
     * @throws \Exception when unable to connect to cache backend
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
     * @return mixed
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