<?php

namespace Phapi\Cache;

use Phapi\Cache;
use Phapi\Exception\Error\CacheNotConnected;
use Phapi\RedisClient;

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
class Redis implements Cache
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
        try {
            $this->cache = new RedisClient($this->servers[0]['host'], $this->servers[0]['port']);
        } catch (\Exception $e) {
            // We don't want to handle an error here since that will stop the execution
            // and we don't want that. A cache shouldn't be a requirement for the application
            // to function and run. We just want to add a notification to the log about it.
            // And that's done by the application while setting up the application.
            return false;
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
        return ('OK' === $this->cache->setex($this->makeKey($key), $this->expire, serialize(['data' => $value]))) ? true : false;
    }

    /**
     * Get something from the cache
     *
     * @param  string $key Key for the value
     * @return mixed
     */
    public function get($key)
    {
        $result = unserialize($this->cache->get($this->makeKey($key)));
        return $result['data'];
    }

    /**
     * Remove something from the cache
     *
     * @param  string $key Key for the value
     * @return boolean
     */
    public function clear($key)
    {
        return (boolean) $this->cache->del($this->makeKey($key));
    }

    /**
     * Check if cache has a value for the key
     *
     * @param  string $key Key for the value
     * @return boolean
     */
    public function has($key)
    {
        return (boolean) $this->cache->exists($this->makeKey($key));
    }

    /**
     * Flush cache
     *
     * @return bool
     */
    public function flush()
    {
        return ($this->cache->flushdb() === 'OK') ? true: false;
    }

    /**
     * Prepare key
     *
     * @param $key
     * @return string
     */
    private function makeKey($key)
    {
        return 'phapi:'. $key;
    }
}