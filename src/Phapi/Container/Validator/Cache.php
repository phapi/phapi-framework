<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Container\Validator;

use Phapi\Cache\NullCache;
use Phapi\Contract\Container;
use Phapi\Contract\Container\Validator;
use Phapi\Contract\Cache as CacheContract;

/**
 * Class Cache
 *
 * @category Phapi
 * @package  Phapi\Container\Validator
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Cache implements Validator {

    /**
     * Dependency Injector Container
     *
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Validate a cache to ensure it implements the Cache Contract and that
     * we are able to connect to the cache backend.
     *
     * If we are unable to connect to the cache backend an Exception should be
     * thrown. That Exception will be handled by the validator.
     *
     * A working cache is NOT a requirement for the application to run so it's
     * important to handle the exception and let the application run.
     *
     * If the exception below is thrown a new NullCache will be created instead.
     *
     * @param $cache
     * @return callable
     */
    public function validate($cache)
    {
        $original = $cache;

        // Make sure the cache is configured using a closure
        if (is_callable($cache)) {
            $failed = false;
            try {
                $cache = $cache($this->container);
            } catch (\Exception $e) {
                $failed = true;
                // Add a note to the log that we are unable to connect to the cache backend
                $this->container['log']->warning(
                    'Unable to connect to the cache backend.'
                );
            }

            // Return original closure if connection didn't fail and if
            // the cache is an instance of the Cache Contract
            if (!$failed && $cache instanceof CacheContract) {
                return $original;
            }
        } else {
            // Add a note to the log that the configuration must be updated
            $this->container['log']->warning(
                'A cache must be configured as a closure. See the documentation for more information.'
            );
        }

        // Return a NullCache as a fallback
        return function ($app) {
            return new NullCache();
        };
    }
}