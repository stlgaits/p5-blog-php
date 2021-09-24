<?php

namespace App\Controller;

use App\TwigRenderer;
use GuzzleHttp\Psr7\Response;

class HomeController
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

    public function index(): Response
    {
        return new Response(200, [], $this->renderer->render('home.html.twig'));
    }
}
