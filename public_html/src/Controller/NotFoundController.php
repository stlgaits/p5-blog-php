<?php

namespace App\Controller;

use App\TwigRenderer;
use GuzzleHttp\Psr7\Response;

class NotFoundController
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
    
    public function notFound():Response
    {
        return new Response(404, [], $this->renderer->render('404.html.twig'));
    }
}
