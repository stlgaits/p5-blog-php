<?php

namespace App\Framework;

use App\Framework\Router\Route;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Route as MezzioRoute;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Register & match routes
 */
class Router
{
    /**
     *
     * @var FastRouteRouter
     */
    private $router;

    public function __construct()
    {
        $this->router = new FastRouteRouter();
    }


    /**
     * Get route
     *
     * @param string $path
     * @param MiddlewareInterface $middleware
     * @param string $name
     * @return void
     */
    public function get(string $path, MiddlewareInterface $middleware, string $name)
    {
        $this->router->addRoute(new MezzioRoute($path, $middleware, ['GET'], $name));
    }

    /**
     * Match route
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {
        $result = $this->router->match($request);
        if ($result->isSuccess()) {
            return new Route(
                $result->getMatchedRouteName(),
                $result,
                $result->getMatchedParams()
            );
        }

        return null;
    }


    public function generateUri(string $name, array $params): ?string
    {
        return $this->router->generateUri($name, $params);
    }
}
