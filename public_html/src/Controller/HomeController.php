<?php

namespace App\Controller;

use App\Auth;
use App\Model\UserManager;
use GuzzleHttp\Psr7\Response;

class HomeController extends DefaultController
{

    /**
     * User auth guard: accessing user methods
     *
     * @var Auth
     */
    private $userAuth;

    public function __construct()
    {
        parent::__construct();
        $this->userAuth = new Auth();
    }

    public function index(): Response
    {
        // change view according to whether user is logged in or not
        if ($this->userAuth->isLoggedIn()) {
            // Display username
            $user = $this->userAuth->getCurrentUser();
            $flashMessage = $this->session->get('flashMessage');
            if(!isset($flashMessage)){
                return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user]));
            } 
            // Remove flash message from user session
            $this->session->delete('flashMessage');
            // Display flash message content to user
            return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user, 'message' => $flashMessage]));
        }
        return new Response(200, [], $this->renderer->render('home.html.twig'));
    }
}
