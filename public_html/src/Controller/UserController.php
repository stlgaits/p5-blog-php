<?php

namespace App\Controller;

use Exception;
use App\Session;
use App\TwigRenderer;
use App\Model\PostManager;
use App\Model\UserManager;
use GuzzleHttp\Psr7\Response;

class UserController
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
     * User manager : PDO connection to Users stored in the database
     *
     * @var UserManager
     */
    private $userManager;

    /**
     *
     * @var ServerRequest
     */
    private $request;

    /**
     *
     * @var Session
     */
    private $session;

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->userManager = new UserManager();
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }

    public function login()
    {
        // redirect user to homepage if user is already logged in
        if (!empty($this->session->get('userID')) && !empty($this->session->get('username'))) {
            return new Response(301, ['Location' => '/']);
        }
        return new Response(200, [], $this->renderer->render('login.html.twig'));
    }


    public function logoutUser()
    {
        // In addition to redirecting user to homepage if already logged in, 
        // we modify the 'login' button to a 'logout' button which then destroys user session
        $this->session->delete('username');
        $this->session->delete('userID');
        $this->session->destroy();
        return new Response(301, ['Location' => '/']);
    }

    public function loginUser()
    {
        $email = $this->request->getParsedBody()['email'];
        $password = $this->request->getParsedBody()['password'];
        $rememberMe = '';
        if (isset($this->request->getParsedBody()['remember-me'])) {
            $rememberMe = $this->request->getParsedBody()['remember-me'];
        } 
        $message = 'Veuillez vérifier vos identifiants de connexion.';
        if (empty($email) || empty($password)) {
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        }
        try {
            $user = $this->userManager->findByEmail($email);
            if (empty($user)) {
                return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
            }
            $actualPassword = $user->getPassword();
            // wrong password input
            if ($password !== $actualPassword) {
                return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
            }
            // 'Remember me' checkbox
            if ($rememberMe === 'remember-me') {
                // store user in cookies
                setcookie('username', $user->getUsername(), time() + (3600 * 24 * 30), null, null, false, true);
            }
            // store user in session
            $this->session->set('username',  $user->getUsername());
            $this->session->set('userID',  $user->getId());
            // successful login redirects to homepage
            return new Response(301, ['Location' => '/']);
        } catch (Exception $e) {
            $user = null;
        return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        }
    }
}
