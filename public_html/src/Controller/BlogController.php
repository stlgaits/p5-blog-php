<?php

namespace App\Controller;

use App\TwigRenderer;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlogController 
{
    /**
     * Twig Environment
     *
     * @var TwigRenderer
     */
    private $environment;

    /**
     * Twig Renderer
     */
    private $renderer;
    
    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
    }
    
    public function list():Response
    {
        return new Response(200, [], $this->renderer->render('blog.html.twig'));
    }


    public function post():Response
    {
        return new Response(200, [], $this->renderer->render('post.html.twig'));
    }

    public function test():Response
    {
        return new Response(200, [], $this->renderer->render('base.html.twig'));
    }

}