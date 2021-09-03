<?php

namespace App;

use App\Router\Route;
use Mezzio\Router\FastRouteRouter;
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
     * @param callable $callable
     * @param string $name
     * @return void
     */
    public function get(string $path, callable $callable, string $name)
    {

    }

    /**
     * Match route
     *
     * @param ServerRequestInterface $request
     * @return Route|null
     */
    public function match(ServerRequestInterface $request): ?Route
    {

    }
}