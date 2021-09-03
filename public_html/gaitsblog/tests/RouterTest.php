<?php

namespace Tests;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase 
{

    public function setUp(): void
    {
        $this->router = new Router();
    }

    public function testGetMethod()
    {
        $request = new Request('GET', '/blog');
        // create URL from router, callback & give a name to the route
        $this->router->get('/blog', function(){ return 'hello';}, 'blog');
        $route =  $this->router->match($request);
        $this->assertEquals('blog', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
    }

    public function testGetMethodIfURLDoesNotExists()
    {
        $request = new Request('GET', '/blog');
        // create URL from router, callback & give a name to the route
        $this->router->get('/grgrgrg', function(){ return 'hello';}, 'blog');
        $route =  $this->router->match($request);
        $this->assertEquals(null, $route);
    }

    public function testGetMethodWithParameters()
    {
        $request = new Request('GET', '/blog/my-slug-7');
        $this->router->get('/blog', function(){ return 'fefefe';}, 'posts');
        //  min alphanumericals  for slug & followed by - then id (numbers only)
        $this->router->get('/blog/{slug:[a-z0-9\-]+}-{id:\d+}', function(){ return 'hello';}, 'post.show');
        $route =  $this->router->match($request);
        $this->assertEquals('post.show', $route->getName());
        $this->assertEquals('hello', call_user_func_array($route->getCallback(), [$request]));
        $this->assertEquals(['slug' => 'my-slug', 'id' => '7'], $route->getParameters);
    }
}