<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Container\Validator;

use Phapi\Contract\Container;
use Phapi\Contract\Container\Validator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class Request
 *
 * @category Phapi
 * @package  Phapi\Container\Validator
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Request implements Validator {

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
     * @trows \RuntimeException when the configured request isn't PSR-7 compatible
     * @param $request
     * @return mixed
     */
    public function validate($request)
    {
        $original = $request;

        // Check if we are using a callable to get the pipeline
        if (is_callable($request) && $request instanceof \Closure) {
            $request = $request($this->container);
        }

        // Check if we have a valid pipeline instance
        if (!$request instanceof ServerRequestInterface) {
            throw new \RuntimeException('The configured request does not implement PSR-7.');
        }

        // All good return original
        return $original;
    }
}