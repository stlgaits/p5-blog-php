<?php

namespace App\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class HomeController  implements MiddlewareInterface
// extends MiddlewareInterface  
// implements RequestHandlerInterface 
{

    public function hello() : Response {

        $body = '<h1>Coucou toi</h1>';
        return new Response(200, [], $body);
    }

    public function index() : Response {

        $body = '<h1>SALUT</h1>';
        return new Response(200, [], $body);
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
    
    // public function handle() ?Response {
    //     return new Response(200, [], 'ALLO');
    // }
}