<?php

namespace Phapi;

/**
 * Bucket
 *
 * Bucket is a helper class that acts like a key/value store. It's can be used
 * to storing configuration and request/response parameters like headers, content
 * and so on.
 *
 * @category Helper
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Bucket implements \Countable
{

    /**
     * Storage container
     *
     * @var array
     */
    protected $storage;

    /**
     * Constructor, accepts and array of key/values
     * to be stored in the bucket
     *
     * @param array $keyValues
     */
    public function __construct(array $keyValues = [])
    {
        $this->storage = $keyValues;
    }

    /**
     * Get all key/values stored in the bucket
     *
     * @return array
     */
    public function all()
    {
        return $this->storage;
    }

    /**
     * Get all keys stored in the bucket
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->storage);
    }

    /**
     * Replace the currently stored key/values in the
     * bucket with these new ones
     *
     * @param array $keyValues
     */
    public function replace(array $keyValues = [])
    {
        $this->storage = $keyValues;
    }

    /**
     * Add key/values to the bucket. Already existing
     * keys (with values) with the same name will be replaces
     *
     * @param array $keyValues
     */
    public function add(array $keyValues = [])
    {
        $this->storage = array_replace($this->storage, $keyValues);
    }

    /**
     * Get a value based on key. If key isn't present in
     * the bucket $default will be returned
     *
     * @param      $key
     * @param null $default
     *
     * @return null|mixed
     */
    public function get($key, $default = null)
    {
        return (array_key_exists($key, $this->storage)) ? $this->storage[$key] : $default;
    }

    /**
     * Set a key and value
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    /**
     * Check if key exists in the bucket
     *
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * Check if key exists and if value is $value
     *
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public function is($key, $value)
    {
        return (array_key_exists($key, $this->storage) && $this->storage[$key] === $value) ? true: false;
    }

    /**
     * Remove a key/value from the bucket
     *
     * @param $key
     */
    public function remove($key)
    {
        unset($this->storage[$key]);
    }

    /**
     * Count how many key/values there are currently
     * stored in the bucket
     *
     * @return int
     */
    public function count()
    {
        return count($this->storage);
    }
}