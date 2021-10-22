<?php

namespace App\Controller;

use App\Session;
use App\TwigRenderer;
use App\Model\UserManager;
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

    /**
     * User session
     *
     * @var Session
     */
    private $session;

    /**
     * User
     *
     * @var UserManager
     */
    private $userManager;
    
    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->session = new Session();
        $this->userManager = new UserManager();
    }

    public function index(): Response
    {
        // change view according to whether user is logged in or not
        if (!empty($this->session->get('userID')) && !empty($this->session->get('username')))
        {
            // Display username 
            $user = $this->userManager->read($this->session->get('userID'));
        }
        return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user]));
    }
}
