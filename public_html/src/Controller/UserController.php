<?php

namespace App\Controller;

use Exception;
use App\Session;
use App\Entity\User;
use App\TwigRenderer;
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

    /**
     * User login View
     *
     * @return Response
     */
    public function login()
    {
        // redirect user to homepage if user is already logged in
        if (!empty($this->session->get('userID')) && !empty($this->session->get('username'))) {
            return new Response(301, ['Location' => '/']);
        }
        return new Response(200, [], $this->renderer->render('login.html.twig'));
    }

    /**
     * Register user View
     *
     * @return Response
     */
    public function register()
    {
        // redirect user to homepage if user is already logged in
        if (!empty($this->session->get('userID')) && !empty($this->session->get('username'))) {
            return new Response(301, ['Location' => '/']);
        }
        return new Response(200, [], $this->renderer->render('register.html.twig'));
    }

    /**
     * Store a new user in Database
     * @return Response
     */
    public function registerUser()
    {
        $message = 'Veuillez renseigner vos futurs identifiants de connexion.';
        $email = $this->request->getParsedBody()['email'];
        $username = $this->request->getParsedBody()['username'];
        $lastName = $this->request->getParsedBody()['last_name'];
        $firstName = $this->request->getParsedBody()['first_name'];
        $password = $this->request->getParsedBody()['password'];
        if(strlen($password) < 8){
            $message = 'Votre mot de passe doit contenir au moins 12 caractères.';
            return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        try {
            // first check that user isn't already stored in database
            if($this->userManager->findByEmail($email) !== null){
                $message = 'Cette adresse email est déjà associée à un compte.';
                return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
            }
            $admin = false;
            $user =  $this->userManager->create($username, $email, $firstName, $lastName, $password, $admin);
            if(empty($user)){
                $message = "Impossible de créer le nouvel utilisateur";
                return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
            }
            $message = "Votre compte a été créé avec succès";
            return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message, 'success' => true]));

        } catch (Exception $e){
            $message = $e->getMessage();
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        }
    }

    /**
     * Redirect user to homepage if already logged in, 
     * modify the 'login' button to a 'logout' button which then destroys user session
     * @return Response (Redirect to homepage)
     */
    public function logoutUser()
    {
        $this->session->delete('username');
        $this->session->delete('userID');
        $this->session->destroy();
        return new Response(301, ['Location' => '/']);
    }

    /**
     * Attempt to retrieve user from database using login form input 
     * Begins user session if successful
     * @return Response
     */
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
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $e->getMessage()]));
        }
    }


    /**
     * Checks whether user is currently logged in
     *
     * @return boolean
     */
    public function isLoggedIn(): bool
    {
        // User credentials are stored in User Session
        if (empty($this->session->get('userID')) && empty($this->session->get('username'))) 
        {
            return false;
        }
        return true;
    }
    
    /**
     * Checks a user's role 
     *
     * @return boolean
     */
    public function isAdmin($user): bool
    {
        if($user->role === false || $user->role === 0){
            return false;
        }
        return true;
    }

    /**
     * Checks whether the current user has admin role
     *
     * @return boolean
     */
    public function isCurrentUserAdmin(): bool
    {
        /**
         * First retrieve user from session
         */
        if($this->isLoggedIn()){
            $user = $this->userManager->read($this->session->get('userID'));
            $role = $user->getRole();
            if($role === false || $role === 0 || $role === '0'){
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Retrieves current user from session storage
     *
     * @return User
     */
    public function getCurrentUser(): User
    {
        return $this->userManager->read($this->session->get('userID'));
    }
}
