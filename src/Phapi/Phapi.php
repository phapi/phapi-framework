<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi;

use Phapi\Cache\NullCache;
use Phapi\Container\Validator\Cache;
use Phapi\Container\Validator\Log;
use Psr\Log\NullLogger;

/**
 * Class Phapi
 *
 * @category Phapi
 * @package  Phapi\Tests
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Phapi extends Container {

    const MODE_DEVELOPMENT = 0;
    const MODE_STAGING = 1;
    const MODE_PRODUCTION = 2;

    public function __construct()
    {
        // Update configuration with default values
        $this->setDefaultConfiguration();

        // Set default logger
        $this->setDefaultLogger();

        // Set default cache
        $this->setDefaultCache();
    }

    /**
     * Update configuration with default values. These values
     * can be overwritten by the developer with their own settings.
     * It is important however that these settings exists and has
     * a value. That's why default needs to be set.
     */
    protected function setDefaultConfiguration()
    {
        // Set mode
        $this['mode'] = self::MODE_DEVELOPMENT;

        // Set http version
        $this['httpVersion'] = '1.1';

        // Set the default accept type
        $this['defaultAccept'] = 'application/json';

        // Set default charset
        $this['charset'] = 'utf-8';

        // Set input
        $this['post'] = $_POST;
        $this['get'] = $_GET;
        $this['server'] = $_SERVER;
        $this['rawContent'] = file_get_contents('php://input');
    }

    /**
     * Set the default logger
     *
     * The PSR-3 package includes a NullLogger that doesn't do anything with
     * the input but it also prevents the application from failing.
     *
     * This simplifies the development since we don't have to check if there
     * actually are a valid cache to use. We can just ask the Cache (even
     * if its a NullCache) and we will get a response.
     */
    protected function setDefaultLogger()
    {
        $this['log'] = function ($app) {
            return new NullLogger();
        };

        // Register validator
        $this->addValidator('log', new Log($this));
    }

    /**
     * Set default cache
     *
     * Phapi includes a NullCache that acts like a dummy cache.
     * Nothing can be saved or retrieved but it makes development
     * easier since it can be used as a "real" cache.
     */
    protected function setDefaultCache()
    {
        $this['cache'] = function ($app) {
            return new NullCache();
        };

        // Register cache validator
        $this->addValidator('cache', new Cache($this));
    }
}