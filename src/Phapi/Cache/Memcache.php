<?php

namespace Phapi\Cache;

use Phapi\Cache;
use Phapi\Exception\Error\CacheNotConnected;

/**
 * Memcache
 *
 * Cache using memcache
 *
 * @category Cache
 * @package  Phapi
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
    }

    /**
     * Connect to the Cache server and make sure we are connected.
     *
     * @return bool
     */
    public function connect()
    {
        // Set up the cache
        $this->cache = new \Memcache();

        // Count the servers to see if we should set up a pool or not
        if (count($this->servers) > 1) {
            // Add all servers
            foreach ($this->servers as $server) {
                $this->cache->addserver($server['host'], $server['port']);
            }
        } else {
            // Only one server is provided. Try and connect to it
            try {
                @$this->cache->connect($this->servers[0]['host'], $this->servers[0]['port']);
            } catch (\Exception $e) {
                // We don't want to handle an error here since that will stop the execution
                // and we don't want that. A cache shouldn't be a requirement for the application
                // to function and run. We just want to add a notification to the log about it.
                // And that's done by the application while setting up the application.
            }
        }

        // Check if we where able to connect
        try {
            $stats = $this->cache->getstats();
            if (is_array($stats)) {
                // We are connected if stats exists (is array)
                return true;
            }
        } catch (\Exception $e) {
            // We don't want to handle an error here since that will stop the execution
            // and we don't want that. A cache shouldn't be a requirement for the application
            // to function and run. We just want to add a notification to the log about it.
            // And that's done by the application while setting up the application.
        }

        // We don't have a connected Cache
        return false;
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