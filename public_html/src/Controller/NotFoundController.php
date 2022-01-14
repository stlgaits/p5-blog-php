<?php

namespace App\Controller;

use GuzzleHttp\Psr7\Response;

class NotFoundController extends DefaultController
{

    public function __construct()
    {
        parent::__construct();
    }
    
    public function notFound(): Response
    {
        // TODO: add conditions on if user then (for navbar)
        return new Response(404, [], $this->renderer->render('404.html.twig'));
    }
}
