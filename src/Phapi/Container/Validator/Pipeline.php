<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Container\Validator;

use Phapi\Contract\Container;
use Phapi\Contract\Pipeline as PipelineContract;
use Phapi\Contract\Container\Validator;

/**
 * Class Pipeline
 *
 * @category Phapi
 * @package  Phapi\Container\Validator
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Pipeline implements Validator {

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
     * Validate middleware pipeline
     *
     * @trows \RuntimeException when the configured pipeline does not implement the Pipeline Contract
     * @param $pipeline
     * @return mixed
     */
    public function validate($pipeline)
    {
        $original = $pipeline;

        // Check if we are using a callable to get the pipeline
        if (is_callable($pipeline) && $pipeline instanceof \Closure) {
            $pipeline = $pipeline($this->container);
        }

        // Check if we have a valid pipeline instance
        if (!$pipeline instanceof PipelineContract) {
            throw new \RuntimeException('The configured pipeline does not implement the Pipeline Contract.');
        }

        // All good return original
        return $original;
    }
}