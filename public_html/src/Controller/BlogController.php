<?php

namespace App\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class BlogController 
// extends RequestHandlerInterface 
implements MiddlewareInterface  
{
    
    public function index():Response
    {
        echo 'Je suis là';
        return new Response(200, [], '<h1>ALLO</h1>');
    }


    public function test():Response
    {
        echo 'Je suis là test ';
        return new Response(200, [], 'ALHUILE');
    }

    public function michel():Response
    {
        echo 'Je suis là michel';
        return new Response(200, [], 'MICHEL');
    }


    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new Response(200, [], 'COUCOU');
    }
}