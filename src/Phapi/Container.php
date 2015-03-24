<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi;

use \Phapi\Contract\Container as Contract;
use \Phapi\Contract\Container\Validator;

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
class Container implements Contract {

    const TYPE_DEFAULT = self::TYPE_SINGLETON;
    const TYPE_SINGLETON = 1;
    const TYPE_MULTITON = 2;

    /**
     * Storage for all keys
     *
     * @var array
     */
    protected $keys = [];

    /**
     * Storage for types
     *
     * @var array
     */
    protected $types = [];

    /**
     * Storage for locked status
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
     * Storage for resolved values
     *
     * @var array
     */
    protected $resolved = [];

    /**
     * Registered validators
     *
     * @var array
     */
    protected $validators = [];

    /**
     * Add a validator that will validate during the binding
     * of a new value/callback.
     *
     * @param $key
     * @param Validator $validator
     */
    public function addValidator($key, Validator $validator)
    {
        $this->validators[$key] = $validator;
    }

    /**
     * Bind/add something to the container
     *
     * @param $key      string  Identifier
     * @param $value    mixed   What to store
     * @param int $type int     Type, singleton or multiton
     */
    public function bind($key, $value, $type = self::TYPE_DEFAULT)
    {
        // Check if locked
        if (isset($this->locked[$key])) {
            throw new \RuntimeException('Cannot override locked content "'. $key .'".');
        }

        // Check if value should be validated
        if (isset($this->validators[$key])) {
            $value = $this->validators[$key]->validate($value);
        }

        // Save key, type and value
        $this->keys[$key] = true;
        $this->types[$key] = $type;
        $this->raw[$key] = $value;
    }

    /**
     * Get something from the container
     *
     * @param $key string Identifier
     * @return mixed
     */
    public function make($key)
    {
        // Check if set
        if (!isset($this->keys[$key])) {
            throw new \InvalidArgumentException('Identifier "'. $key .'" is not defined.');
        }

        // Check if it is a simple value (string, int etc) that
        // should be returned
        if (
            !is_object($this->raw[$key])
            || !method_exists($this->raw[$key], '__invoke')
        ) {
            return $this->raw[$key];
        }

        if ($this->types[$key] === self::TYPE_SINGLETON) {
            if (isset($this->resolved[$key])) {
                return $this->resolved[$key];
            }

            $this->locked[$key] = true;
            return $this->resolved[$key] = $this->raw[$key]($this);
        }

        // Return multiton
        return $this->raw[$key]($this);
    }

    /**
     * ArrayAccess set/bind
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->bind($offset, $value);
    }

    /**
     * ArrayAccess unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset(
            $this->keys[$offset],
            $this->locked[$offset],
            $this->raw[$offset],
            $this->resolved[$offset],
            $this->types[$offset]
        );
    }

    /**
     * ArrayAccess check if exists
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->keys[$offset]);
    }

    /**
     * ArrayAccess get/make
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->make($offset);
    }
}