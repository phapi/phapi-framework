<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi;

/**
 * Class Container
 *
 * Dependency Injector Container
 *
 * @category Phapi
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Container {

    /**
     * Storage for all keys
     *
     * @var array
     */
    protected $keys = [];

    /**
     * Storage for values
     *
     * @var array
     */
    protected $values = [];

    /**
     * Factory storage
     *
     * @var \SplObjectStorage
     */
    protected $factories;

    /**
     * Storage for keys of locked values
     *
     * @var array
     */
    protected $locked = [];

    /**
     * Storage for raw values
     *
     * @var array
     */
    protected $raw = [];

    /**
     * Create Dependency Injection Container
     *
     * @param array $values
     * @throws \Exception
     */
    public function __construct(array $values = [])
    {
        // Create factory storage
        $this->factories = new \SplObjectStorage();

        // Add values to the container
        foreach ($values as $key => $value) {
            $this->__set($key, $value);
        }
    }

    /**
     * Mark callable to use factory
     *
     * @param $callable
     * @return mixed
     */
    public function factory($callable)
    {
        $this->factories->attach($callable);

        return $callable;
    }

    /**
     * Set value (parameter or object)
     *
     * @param $key
     * @param $value
     * @throws \Exception
     */
    public function __set($key, $value)
    {
        if (isset($this->locked[$key])) {
            throw new \RuntimeException('Cannot override locked service "'. $key .'".');
        }

        $this->values[$key] = $value;
        $this->keys[$key] = true;
    }

    /**
     * Get value (parameter or object)
     *
     * @param $key
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function __get($key)
    {
        if (!isset($this->keys[$key])) {
            throw new \InvalidArgumentException('Identifier "'. $key .'" is not defined.');
        }

        if (
            isset($this->raw[$key])
            || !is_object($this->values[$key])
            || !method_exists($this->values[$key], '__invoke')
        ) {
            return $this->values[$key];
        }

        if (isset($this->factories[$this->values[$key]])) {
            return $this->values[$key]($this);
        }

        $raw = $this->values[$key];
        $val = $this->values[$key] = $raw($this);
        $this->raw[$key] = $raw;
        $this->locked[$key] = true;
        return $val;
    }

    /**
     * Check if key is set (with an object or parameter)
     *
     * @param string $key The unique key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->keys[$key]);
    }

    /**
     * Unset a value based on key
     *
     * @param mixed $key
     */
    public function __unset($key)
    {
        if (isset($this->keys[$key])) {
            if (is_object($this->values[$key])) {
                unset($this->factories[$this->values[$key]]);
            }
            unset($this->values[$key], $this->locked[$key], $this->raw[$key], $this->keys[$key]);
        }
    }
}