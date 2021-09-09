<?php

namespace Tests\Framework;

use App\Framework\Router;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\RouterInterface;
use Psr\Http\Server\MiddlewareInterface;
use Mezzio\Router\Middleware\RouteMiddleware;

class RouterTest extends TestCase
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var MiddlewareInterface
     */
    private $middleware;

    /**
     * @var RouterInterface
     */
    private $fastRouteRouter;

    public function setUp(): void
    {
        $this->router = new Router();
        $this->fastRouteRouter = new FastRouteRouter();
        $this->middleware = new RouteMiddleware($this->fastRouteRouter);
    }

    public function testGetMethod()
    {
        $request = new ServerRequest('GET', '/blog');

        // create URL from router, callback & give a name to the route
        // $this->router->get('/blog', function(){ return 'hello';}, 'blog');
        $this->router->get('/blog', $this->middleware, 'blog');
        $route =  $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        // $this->assertEquals('hello', call_user_func_array($route->getMiddleware(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new ServerRequest('GET', '/blog');
        // create URL from router, callback & give a name to the route
        // $this->router->get('/grgrgrg', function(){ return 'hello';}, 'blog');
        $this->router->get('/grgrgrg', $this->middleware, 'blog');
        $route =  $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new ServerRequest('GET', '/blog/my-slug-7');
        // $this->router->get('/blog', function(){ return 'fefefe';}, 'posts');
        $this->router->get('/blog', $this->middleware, 'posts');
        //  min alphanumericals  for slug & followed by - then id (numbers only)
        // $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function(){ return 'hello';}, 'post.show');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', $this->middleware, 'post.show');
        $route =  $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        // $this->assertEquals('hello', call_user_func_array($route->getMiddleware(), [$request]));
        $this->assertEquals(['slug' => 'my-slug', 'id' => '7'], $route->getParams());
        // test invalid URL
        $route = $this->router->match(new ServerRequest('GET', '/blog/my_slug-7'));
        $this->assertEquals(null, $route);
    }

    public function testGenerateUri()
    {
        // $this->router->get('/blog', function(){ return 'fefefe';}, 'posts');
        $this->router->get('/blog', $this->middleware, 'posts');
        // $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function(){ return 'hello';}, 'post.show');
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', $this->middleware, 'post.show');
        $uri = $this->router->generateUri('post.show', ['slug' => 'my-post', 'id' => 18]);
        $this->assertEquals('/blog/my-post-18', $uri);
    }


    // public function testQuiMarchePas(){
    //     $request = new ServerRequest('GET', '/courgette');
    //     $this->router->get('/courgette', $this->middleware, 'cornichon');
    //     $route =  $this->router->match($request);
    //     $this->assertEquals('cornichon', $route->getName());
    // }
}
