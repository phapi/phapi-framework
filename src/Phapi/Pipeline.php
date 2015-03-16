<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi;

use Phapi\Contract\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class Pipeline
 *
 * This class implements a pipe-line of middleware, which can
 * be attached using the 'pipe()'-method.
 *
 * @category Phapi
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Pipeline implements Middleware {

    /**
     * Queue of middleware
     *
     * @var \SplQueue
     */
    protected $queue;

    /**
     * Dependency Injector Container
     *
     * @var mixed
     */
    protected $container;

    /**
     * Lock status of the pipeline
     *
     * @var bool
     */
    protected $locked = false;

    public function __construct($container = null)
    {
        $this->queue = new \SplQueue();
        $this->container = $container;
    }

    /**
     * Add middleware to the pipe-line. Please note that middleware's will
     * be called in the order they are added.
     *
     * A middleware CAN implement the Middleware Interface, but MUST be
     * callable. A middleware WILL be called with three parameters:
     * Request, Response and Next.
     *
     * @param callable $middleware
     */
    public function pipe(callable $middleware)
    {
        // Check if the pipeline is locked
        if ($this->locked) {
            throw new \RuntimeException('Middleware can’t be added once the stack is dequeuing');
        }

        if (method_exists($middleware, 'setDI') && $this->container !== null) {
            $middleware->setDI($this->container);
        }

        // Add the middleware to the queue
        $this->queue->enqueue($middleware);
    }

    /**
     * Handle the request by calling the next middleware in the queue.
     *
     * The method requires that a request and a response are provided.
     * These will be passed to any middleware invoked.
     *
     * Once the queue is empty the resulted response instance will be
     * returned.
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        // Lock the pipeline
        $this->locked = true;

        // Check if the pipe-line is broken or if we are at the end of the queue
        if (!$this->queue->isEmpty()) {
            // Pick the next middleware from the queue
            $next = $this->queue->dequeue();

            // Call the next middleware (if callable)
            return (is_callable($next)) ? $next($request, $response, $this): $response;
        }

        // Nothing left to do, return the response
        return $response;
    }
}