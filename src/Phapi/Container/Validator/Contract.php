<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Container\Validator;

use Phapi\Contract\Container;
use Phapi\Contract\Container\Validator;

/**
 * Class Contract
 *
 * Validate that an object implements a specific contract.
 *
 * @category Phapi
 * @package  Phapi\Container\Validator
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Contract implements Validator {

    /**
     * Dependency Injector Container
     *
     * @var Container
     */
    protected $container;

    /**
     * @var mixed
     */
    protected $contract;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    /**
     * Validate middleware pipeline
     *
     * @trows \RuntimeException when the configured pipeline does not implement the Pipeline Contract
     * @param $value
     * @return mixed
     */
    public function validate($value)
    {
        $original = $value;

        // Check if we are using a callable to get the pipeline
        if (is_callable($value) && $value instanceof \Closure) {
            $value = $value($this->container);
        }

        // Check if we have a valid pipeline instance
        if (!$value instanceof $this->contract) {
            throw new \RuntimeException('The configured value does not implement '. $this->contract);
        }

        // All good return original
        return $original;
    }
}