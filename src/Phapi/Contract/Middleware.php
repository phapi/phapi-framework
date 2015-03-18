<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Contract;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Interface Middleware
 *
 * Middleware accepts a request and a response and optionally a callback
 * ($next) that is called if the middleware allows further middleware to
 * process the request.
 *
 * If a middleware doesn't need or desire to allow further processing it
 * should not call the callback ($next) and should instead return a response.
 *
 * @category Phapi
 * @package  Phapi\Contract
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
interface Middleware {

    /**
     * Process an incoming request and/or response.
     *
     * Accepts a request and a response (PSR-7 compatible) instance and
     * does something with them.
     *
     * The middleware must pass a request and a response to the ($next)
     * callback and finally either return the response from the ($next)
     * callback or by modifying the response before returning it.
     *
     * Examples:
     * return $next();
     *
     * OR
     *
     * $response = $next();
     * // Modify response
     * ...
     * return $response;
     *
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next = null);

}