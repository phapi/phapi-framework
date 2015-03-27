<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Contract\Container;

use Phapi\Contract\Container;

/**
 * Interface ContainerValidator
 *
 * @category Phapi
 * @package  Phapi\Contract
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
interface Validator {

    /**
     * Set the Dependency Injection Container
     *
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * Takes the value/closure and validates it.
     *
     * If validation passes the value/closure should be returned.
     * Else a default should be returned.
     *
     * @param $value
     * @return mixed
     */
    public function validate($value);

}