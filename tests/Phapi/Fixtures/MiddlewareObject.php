<?php
/**
 * This file is part of Phapi.
 *
 * See license.md for information about the license.
 */

namespace Phapi\Tests\Fixtures;

use Phapi\Contract\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


/**
 * Class One
 *
 * @category Phapi
 * @package  Phapi\Middleware
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class MiddlewareObject implements Middleware {

    public function __invoke(
        Request $request,
        Response $response,
        callable $next = null
    ) {
        $response = $response->withAddedHeader('X-Foo', 'modified');
        $response = $next($request, $response, $next);
        return $response;
    }

    public function setDI($container = null) {
        $this->container = $container;
    }

}