<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Cache;

use Phapi\Contract\Cache;

/**
 * NullCache
 *
 * The NullCache simulates a cache but isn't caching anything. This
 * simplifies the development since we don't have to check if there
 * actually are a valid cache to use. We can just ask the Cache (even
 * if its a NullCache) and we will get a response.
 *
 * @category Cache
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class NullCache implements Cache {

    public function connect()
    {
        return true;
    }

    public function clear($key)
    {
        return true;
    }

    public function flush()
    {
        return true;
    }

    public function get($key)
    {
        return false;
    }

    public function set($key, $value)
    {
        return false;
    }

    public function has($key)
    {
        return false;
    }
}