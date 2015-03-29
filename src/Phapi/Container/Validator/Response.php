<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Container\Validator;

use Phapi\Contract\Container;
use Phapi\Contract\Container\Validator;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Response
 *
 * @category Phapi
 * @package  Phapi\Container\Validator
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Response implements Validator {

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
     * Validate HTTP response object
     *
     * @trows \RuntimeException when the configured response is not PSR-7 compatible
     * @param $response
     * @return mixed
     */
    public function validate($response)
    {
        $original = $response;

        // Check if we are using a callable to get the pipeline
        if (is_callable($response) && $response instanceof \Closure) {
            $response = $response($this->container);
        }

        // Check if we have a valid pipeline instance
        if (!$response instanceof ResponseInterface) {
            throw new \RuntimeException('The configured response does not implement PSR-7.');
        }

        // All good return original
        return $original;
    }
}