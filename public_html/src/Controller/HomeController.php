<?php

namespace App\Controller;

use App\Session;
use App\TwigRenderer;
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
     * User Controller
     *
     * @var UserController
     */
    private $userController;

    public function __construct()
    {
        parent::__construct();
        $this->userManager = new UserManager();
        $this->userController = new UserController();
    }

    public function index(): Response
    {
        // var_dump($_SESSION);
        // // var_dump($this->session);
        // // var_dump($this->session->get('flashMessage'));
        // // foreach($this->session as $stf){
        //     //     var_dump($stf);
        //     // }
        //     var_dump($this->session->getStatus());
        // $this->session->reset();

        //     var_dump($_SESSION);
        // var_dump($this->session->getStatus());
        // change view according to whether user is logged in or not
        if ($this->userController->isLoggedIn()) {
            // Display username
            $user = $this->userManager->read($this->session->get('userID'));
            return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user]));
        }
        return new Response(200, [], $this->renderer->render('home.html.twig'));
    }
}
