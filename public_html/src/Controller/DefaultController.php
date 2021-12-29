<?php

namespace App\Controller;

use App\Mailer;
use App\Session;
use App\TwigRenderer;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Parent of all controllers
 */
class DefaultController
{
    /**
     *
     * @var ServerRequest
     */
    protected $request;

    /**
     * Twig Environment
     *
     * @var TwigRenderer
     */
    protected $environment;

    /**
     * Twig Renderer
     */
    protected $renderer;

    /**
     * User session
     *
     * @var Session
     */
    protected $session;

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }


}