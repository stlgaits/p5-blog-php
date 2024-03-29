<?php

namespace App\Controller;

use App\Auth;
use Exception;
use App\Entity\User;
use App\Model\UserManager;
use GuzzleHttp\Psr7\Response;
use App\Controller\DefaultController;

class UserController extends DefaultController
{

    /**
     * User manager : PDO connection to Users stored in the database
     *
     * @var UserManager
     */
    private $userManager;

    /**
     * User Auth / guard
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

    /**
     * User login View
     *
     * @return Response
     */
    public function login(): Response
    {
        // redirect user to homepage if user is already logged in
        if ($this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToHomePage();
        }
        return new Response(200, [], $this->renderer->render('login.html.twig'));
    }

    /**
     * Register user View
     *
     * @return Response
     */
    public function register(): Response
    {
        // redirect user to homepage if user is already logged in
        if ($this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToHomePage();
        }
        return new Response(200, [], $this->renderer->render('register.html.twig'));
    }

    /**
     * Get user profile's View TODO: finish this view
     *
     * @return Response
     */
    public function profile(): Response
    {
        // redirect user to homepage if user isn't logged in
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToHomePage();
        }
        $user = $this->userAuth->getCurrentUser();
        $flashMessage = $this->session->get('flashMessage');
        if (isset($flashMessage)) {
            // Remove flash message from user session before displaying it
            $this->session->delete('flashMessage');
            return new Response(200, [], $this->renderer->render('profile.html.twig', ['user' => $user, 'message' => $flashMessage]));
        }
        return new Response(200, [], $this->renderer->render('profile.html.twig', ['user' => $user]));
    }

    /**
     * Store a new user in Database
     *
     * @return Response
     */
    public function registerUser(): Response
    {
        $message = 'Veuillez renseigner vos futurs identifiants de connexion.';
        $email = htmlspecialchars($this->request->getParsedBody()['email']);
        $username = htmlspecialchars($this->request->getParsedBody()['username']);
        $lastName = htmlspecialchars($this->request->getParsedBody()['last_name']);
        $firstName = htmlspecialchars($this->request->getParsedBody()['first_name']);
        $password = htmlspecialchars($this->request->getParsedBody()['password']);
        if (strlen($password) < 12) {
            $message = 'Votre mot de passe doit contenir au moins 12 caractères.';
            return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
        }
        $password = password_hash($password, PASSWORD_DEFAULT);
        try {
            // first check that user isn't already stored in database
            if ($this->userManager->findByEmail($email) !== null) {
                $message = 'Cette adresse email est déjà associée à un compte.';
                return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
            }
            // check if username doesn't already exist
            if ($this->userManager->findByUsername($username) !== null) {
                $message = 'Ce pseudo est déjà associé à un compte.';
                return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
            }
            $admin = 0;
            $user =  $this->userManager->create($username, $email, $firstName, $lastName, $password, $admin);
            if (empty($user)) {
                $message = "Impossible de créer le nouvel utilisateur";
                return new Response(200, [], $this->renderer->render('register.html.twig', ['message' => $message]));
            }
            $message = "Votre compte a été créé avec succès";
            $this->session->set('flashMessage', $message);
            return $this->loginUser();
        } catch (Exception $e) {
            $message = $e->getMessage();
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        }
    }

    /**
     * Redirect user to homepage if already logged in,
     * modify the 'login' button to a 'logout' button which then destroys user session
     *
     * @return Response (Redirect to homepage)
     */
    public function logoutUser(): Response
    {
        $this->session->delete('username');
        $this->session->delete('userID');
        if ($this->session->get('flashMessage')) {
            $this->session->delete('flashMessage');
        }
        $this->session->destroy();
        return $this->redirect->redirectToHomePage();
    }

    /**
     * Attempt to retrieve user from database using login form input
     * Begins user session if successful
     *
     * @return Response
     */
    public function loginUser(): Response
    {
        $email = htmlspecialchars($this->request->getParsedBody()['email']);
        $password = htmlspecialchars($this->request->getParsedBody()['password']);
        $rememberMe = '';
        if (isset($this->request->getParsedBody()['remember-me'])) {
            $rememberMe = htmlspecialchars($this->request->getParsedBody()['remember-me']);
        }
        $message = 'Veuillez vérifier vos identifiants de connexion.';
        if (empty($email) || empty($password)) {
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
        }
        try {
            $user = $this->userManager->findByEmail($email);
            if (empty($user) || $this->userAuth->isDisabled($user)) {
                return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
            }
            $actualPassword = $user->getPassword();
            // Check password input against hashed password in database
            if (!password_verify($password, $actualPassword)) {
                // wrong password input
                return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $message]));
            }
            // 'Remember me' checkbox
            if ($rememberMe === 'remember-me') {
                // store user in cookies
                setcookie('username', $user->getUsername(), time() + (3600 * 24 * 30), null, null, false, true);
            }
            // store user in session
            $this->session->set('username', $user->getUsername());
            $this->session->set('userID', $user->getId());
            // successful login redirects to homepage
            return $this->redirect->redirectToHomePage();
        } catch (Exception $e) {
            $user = null;
            return new Response(200, [], $this->renderer->render('login.html.twig', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Updates a user info
     * @param array $data
     * @return Response
     */
    public function editProfile(int $id)
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $flashMessage = '';
        $userToUpdate = $this->userManager->read($id);
        $data = $this->request->getParsedBody();
        foreach ($data as $key => $value) {
            $value = htmlspecialchars($value);
            $setter = 'set' . ucfirst($key);
            if ($value !== $userToUpdate->__get($key)) {
                if ($key !== 'password' && $key !== 'id' && $key !== '_CSRF_INDEX' && $key !== '_CSRF_TOKEN') {
                    $userToUpdate->$setter($value);
                } else {
                    // TODO: password is hashed so we treat it differently
                }
            }
        }
        try {
            // database query
            $this->userManager->update(
                intval($userToUpdate->getId()),
                $userToUpdate->getUsername(),
                $userToUpdate->getEmail(),
                $userToUpdate->getFirst_name(),
                $userToUpdate->getLast_name(),
                $userToUpdate->getPassword(),
                intval($userToUpdate->getRole()),
            );
            $flashMessage = 'Le profil a été modifié avec succès.';
        } catch (Exception $e) {
            $flashMessage = 'Il y a eu une erreur : ' . $e->getMessage();
        }
        $this->session->set('flashMessage', $flashMessage);
        return $this->redirect->redirectToPreviousPage();
    }

    /**
     * TODO:
     * Updates a user from Admin's dashboard
     *
     * @return void
     */
    public function editUserProfile()
    {
    }

    //TODO: allow admins (from admindb so admincontroller) to promote & demote users => create ROUTES for this in index.php (utiliser méthode update direct sinon?)
    public function promoteUserToRoleAdmin(int $id)
    {
        $this->userManager->promote($id);
    }

    public function demoteUser(int $id)
    {
        $this->userManager->demote($id);
    }
}
