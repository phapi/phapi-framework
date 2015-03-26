<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Container\Validator;

use Phapi\Contract\Container;
use Phapi\Contract\Container\Validator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class Log
 *
 * @category Phapi
 * @package  Phapi\Container\Validator
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Log implements Validator {

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
     * Validates the configured logger. If no logger is configured or if the configured
     * logger isn't PSR-3 compliant an instance of NullLogger will be used instead.
     *
     * The PSR-3 package includes a NullLogger that does not do anything with
     * the input but it also prevents the application from failing.
     *
     * This simplifies the development since we don't have to check if there
     * actually are a valid cache to use. We can just ask the Cache (even
     * if its a NullCache) and we will get a response.
     *
     * @param $logger
     * @return callable
     */
    public function validate($logger)
    {
        $original = $logger;

        if (is_callable($logger)) {
            $logger = $logger($this->container);
        }

        // Check if logger is an instance of the PSR-3 logger interface
        if ($logger instanceof LoggerInterface) {
            return $original;
        }

        // A PSR-3 compatible log writer hasn't been configured so we don't know if it is
        // compatible with Phapi. Therefore we create an instance of the NullLogger instead
        return function ($app) {
            return new NullLogger();
        };
    }
}