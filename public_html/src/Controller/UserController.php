<?php

namespace App\Controller;

use Exception;
use App\Session;
use App\TwigRenderer;
use App\Model\PostManager;
use App\Model\UserManager;
use GuzzleHttp\Psr7\Response;
use stdClass;

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
     *  TODO: verify whether user's email already set in database to prevent duplicates (unique constraint)
     *  TODO: constraints on password input : minimum length of 8 chars
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
        $password = password_hash($password, PASSWORD_DEFAULT);
        // if (empty($email) || empty($password) || empty($username) || empty($lastName || empty($firstName || empty()))) {
        //     return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
        // }
        try {
            // first check that user isn't already stored in database
            if($this->userManager->findByEmail($email) !== null){
                $message = 'Cette adresse email est déjà associée à un compte.';
                return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
            }
            //TODO: find a way to fix this PROPERLY to get a decent role atttribution strategy
            // $roles = ['ROLE_USER', 'ROLE_ADMIN'];
            // $roles = new \stdClass;
            // // $roles = 'ROLE_USER';
            // $object = (object) [ '0' => 'ROLE_USER'];
            // $string = strval($object);
            // $bdd = '{"0": "ROLE_USER","1": "ROLE_EDITOR"}';
            // var_dump($bdd);
            // var_dump($string);
            // var_dump(json_decode($string));
            // // var_dump($roles);
            // // var_dump(json_encode($roles));
            
            // die();
            $roles = "ROLE_USER";
            $user =  $this->userManager->create($username, $email, $firstName, $lastName, $password, json_encode($roles));
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
}
