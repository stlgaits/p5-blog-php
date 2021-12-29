<?php

namespace App\Controller;

use App\Mailer;
use App\Session;
use App\TwigRenderer;

/**
 * Parent of all controllers
 */
class DefaultController
{
    /**
     *
     * @var ServerRequest
     */
    private $request;

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

    /**
     * User session
     *
     * @var Session
     */
    private $session;

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        // $this->mailer = new Mailer();
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }


}