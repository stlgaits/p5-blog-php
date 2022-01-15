<?php

namespace App\Controller;

use App\Auth;
use App\Model\UserManager;
use GuzzleHttp\Psr7\Response;

class HomeController extends DefaultController
{
    /**
     * User Manager - accessing users in database
     *
     * @var UserManager
     */
    private $userManager;

    /**
     * User auth guard
     *
     * @var Auth
     */
    private $userAuth;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
        $this->userAuth = new Auth();
    }

    public function index(): Response
    {
        // change view according to whether user is logged in or not
        if ($this->userAuth->isLoggedIn()) {
            // Display username
            $user = $this->userManager->read($this->session->get('userID'));
            return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user]));
        }
        return new Response(200, [], $this->renderer->render('home.html.twig'));
    }
}
