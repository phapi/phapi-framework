<?php
namespace Phapi;

use Phapi\Exception\Error\MethodNotAllowed;
use Phapi\Exception\Error\NotFound;
use Phapi\Exception\MethodNotAllowedException;
use Phapi\Exception\RouteNotFoundException;

/**
 * Dispatcher class
 *
 * Class dispatching routes based on the made request.
 *
 * @category Router
 * @package  Phapi
 * @author   Peter Ahinko <peter@ahinko.se>
 * @license  MIT (http://opensource.org/licenses/MIT)
 * @link     https://github.com/ahinko/phapi
 */
class Dispatcher {

    /**
     * Router object
     *
     * @var Router
     */
    protected $router;

    /**
     * Constructor
     *
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Dispatch the request. Check if resource and method exists.
     * Return whatever the resource returns.
     *
     * @param mixed $app
     * @return mixed
     * @throws MethodNotAllowed
     * @throws NotFound
     */
    public function dispatch($app = null)
    {
        $resourceName = $this->router->getMatchedResource();
        $methodName = $this->router->getMatchedMethod();

        if (class_exists($resourceName)) {
            $resource = new $resourceName($app);
            if (method_exists($resource, $methodName)) {
                return $resource->$methodName();
            }
            throw new MethodNotAllowed();
        }

        throw new NotFound();
    }
}