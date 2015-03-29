<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Contract;

/**
 * Interface Pipeline
 *
 * Pipeline is the storage/queue for the piped/added middleware. The
 * pipeline executes the chaining and starts dequeueing process.
 *
 * @category Phapi
 * @package  Phapi\Contract
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
interface Pipeline extends Middleware {

    /**
     * Add middleware to the pipe-line. Middleware should be called in the
     * same order they are piped.
     *
     * A middleware CAN implement the Middleware Interface, but MUST be
     * callable. A middleware WILL be called with three parameters:
     * Request, Response and Next.
     *
     * @throws \RuntimeException when adding middleware to the stack to late
     * @param callable $middleware
     */
    public function pipe(callable $middleware);

}