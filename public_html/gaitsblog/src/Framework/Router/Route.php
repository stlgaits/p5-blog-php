<?php

namespace App\Framework\Router;

use Psr\Http\Server\MiddlewareInterface;

/**
 * Represents a matched route
 */
class Route
{

    /**
     * @var string
     */
    private $name;

    /**
     *
     * @var MiddlewareInterface
     */
    private $middleware;
    
    /**
     * @var array
     */
    private $parameters;


    public function __construct(string $name, MiddlewareInterface $middleware, array $parameters)
    {
        $this->name = $name;
        $this->middleware = $middleware;
        $this->parameters = $parameters;
    }

    /**
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return MiddlewareInterface
     */
    public function getMiddleware(): MiddlewareInterface
    {
        return $this->middleware;
    }

    /**
     * Retrieve URL parameters
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->parameters;
    }
}
