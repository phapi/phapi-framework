<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Cache;

use Phapi\Contract\Cache;

/**
 * Class Memcache
 *
 * Cache using Memcache as backend
 *
 * @category Phapi
 * @package  Phapi\Cache
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Memcache implements Cache
{
    /**
     * Cache connection
     *
     * @var \Memcache
     */
    protected $cache;

    /**
     * List of servers
     *
     * @var array
     */
    protected $servers;

    /**
     * Compression
     *
     * @var boolean
     */
    protected $compression;

    /**
     * Expiration time
     *
     * @var int
     */
    protected $expire;

    /**
     * Create cache
     *
     * @param array $servers
     * @param bool $compression
     * @param int $expire
     */
    public function __construct($servers = [], $compression = false, $expire = 3600)
    {
        // Set compression
        $this->compression = $compression;
        // Set expire
        $this->expire = $expire;
        $this->servers = $servers;

        // Set up the cache
        $this->cache = new \Memcache();

        // Add servers and connect
        foreach ($this->servers as $server) {
            $this->cache->addserver($server['host'], $server['port']);
        }

        // Check if we are connected to the memcache backend
        if (! @$this->cache->getstats()) {
            // If not, throw an exception that will be handled by the method
            // calling this connect method. A working cache is NOT a requirement
            // for the application to run so it's important to handle the exception
            // and let the application run. Suggestion: if the exception below is
            // thrown a new NullCache should be created
            throw new \Exception('Unable to connect to Memcache backend');
        }

        return true;
    }

    /**
     * Set/add something to the cache
     *
     * @param  string $key Key for the value
     * @param  mixed  $value Value
     * @return boolean
     */
    public function set($key, $value)
    {
        if ($this->cache->add($key, $value, $this->compression, $this->expire)) {
            return true;
        }
        return $this->cache->replace($key, $value, $this->compression, $this->expire);
    }

    /**
     * Get something from the cache
     *
     * @param  string $key Key for the value
     * @return mixed
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * Remove something from the cache
     *
     * @param  string $key Key for the value
     * @return boolean
     */
    public function clear($key)
    {
        return $this->cache->delete($key);
    }

    /**
     * Check if cache has a value for the key
     *
     * @param  string $key Key for the value
     * @return boolean
     */
    public function has($key)
    {
        return (boolean) $this->cache->get($key);
    }

    /**
     * Flush cache
     *
     * @return bool
     */
    public function flush()
    {
        return $this->cache->flush();
    }
}