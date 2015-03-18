<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi;

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
}