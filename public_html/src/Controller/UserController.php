<?php

namespace App\Controller;

use App\TwigRenderer;
use App\Model\PostManager;
use GuzzleHttp\Psr7\Response;
use App\Model\UserManager;
use Exception;

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

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->userManager = new UserManager();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }

    public function login()
    {
        session_start();
        // redirect user to homepage (or make this page hidden ?) if user is already logged in
        if (isset($_SESSION['username'])) {
            // redirect to Homepage
            return new Response(301, ['Location' => '/']);
        }
        return new Response(200, [], $this->renderer->render('login.html.twig'));
    }


    public function logoutUser()
    {
        //TODO: instead of redirecting user to homepage if already logged in, I want to modify the 'login' button to a 'logout' button which
        // would then remove user session & redirect to login form
    }

    public function loginUser()
    {
        try {
            session_start();
            $email = $this->request->getParsedBody()['email'];
            $password = $this->request->getParsedBody()['password'];
            if (!isset($this->request->getParsedBody()['remember-me'])) {
                $rememberMe = '';
            } else {
                $rememberMe = $this->request->getParsedBody()['remember-me'];
            }
            $message = 'Veuillez vérifier vos identifiants de connexion.';
            if (empty($email) || empty($password)) {
                session_destroy();
                return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
            }
            try {
                $user = $this->userManager->findByEmail($email);
                if (empty($user)) {
                    session_destroy();
                    return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
                }
                $actualPassword = $user->getPassword();
                // wrong password input
                if ($password !== $actualPassword) {
                    session_destroy();
                    return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
                }
                // 'Remember me' checkbox
                if ($rememberMe === 'remember-me') {
                    // store user in cookies
                    setcookie('username', $user->getUsername(), time() + (3600 * 24 * 30), null, null, false, true);
                }
                // store user in session
                $_SESSION['username'] = $user->getUsername();
                // TODO: remplacer par une redirection pour notamment avoir la bonne URI + alléger le code
                return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user]));
            } catch (Exception $e) {
                $user = null;
                session_destroy();
            }
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        } catch (Exception $e) {
            session_destroy();
        }
    }
}
