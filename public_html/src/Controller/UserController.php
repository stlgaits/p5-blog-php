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

    
    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->userManager = new UserManager();
    }

    public function login()
    {
        return new Response(200, [], $this->renderer->render('login.html.twig'));
    }

    public function loginUser()
    {
        session_start();
        //TODO:  inverser la logique => exception en premier 'fail first'
        if(isset($_POST['email'])){
            try {
                $user = $this->userManager->findByEmail($_POST['email']);
            } catch (Exception $e){
                $user = null;
            }
            if(!empty($user)){
                if(isset($_POST['password'])){
                    $actualPassword = $user->getPassword();
                    $inputPassword = $_POST['password'];      
                    // compare password input from the form to actual password recorded in the database
                    if($actualPassword === $inputPassword){
                        // 'Remember me' checkbox
                        if(isset($_POST['remember-me']) && $_POST['remember-me'] === 'remember-me'){
                            // store user in cookies
                            setcookie('username', $user->getUsername(), time()+(3600*24*30),null, null, false, true);
                            setcookie('password', $user->getPassword(), time()+(3600*24*30),null, null, false, true);
                        }
                        $_SESSION['username'] = $user->getUsername();
                        $_SESSION['password'] = $user->getPassword();

                        // TODO: remplacer par une redirection pour notamment avoir la bonne URI + alléger le code
                        return new Response(200, [], $this->renderer->render('home.html.twig', ['user' => $user]));
                    }
                    $message = 'Mot de passe incorrect';
                    session_destroy();
                    // throw new Exception('Mot de passe incorrect'); 
                    
                    return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
                }
                $message = 'Veuillez renseigner votre mot de passe'; 
                session_destroy();
                // throw new Exception('Veuillez renseigner votre mot de passe.');
                return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
            }
            $message = 'L\'adresse email est incorrecte.';
            session_destroy();
            // throw new Exception('L\'adresse email est incorrecte.');
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        }
        $message = 'Veuillez renseigner vos informations de connexion.';
        session_destroy();
        // throw new Exception ('Veuillez renseigner vos informations de connexion.');
        return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
    }

}