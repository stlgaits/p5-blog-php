<?php

namespace App\Controller;

use App\Session;
use App\TwigRenderer;
use App\Model\PostManager;
use App\Model\UserManager;
use GuzzleHttp\Psr7\Response;

class AdminController
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
     * PostManager
     * @var PostManager
     */
    private $postManager;

    /**
     * UserManager
     *
     * @var UserManager
     */
    private $userManager;
    
    /**
     * Current User Session
     *
     * @var Session
     */
    private $session;

    /**
     *
     * @var ServerRequest
     */
    private $request;


    /**
     * User Controller
     *
     * @var UserController
     */
    private $userController;

    /**
     * Currently logged in user
     *
     * @var User|boolean
     */
    private $user;

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
        $this->userController = new UserController();
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
        // only allow access to users who are both logged in and have admin role
        if($this->userController->isCurrentUserAdmin() === false){
            //TODO: bugfix why would this work with isLoggedIn() but not with iscrrentUserAdmin ???? despite clearly getting a false result and entering condition
            return new Response(301, ['Location' => 'login']);
        }
        
    }

    public function index(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $this->user = $this->userController->getCurrentUser();
        return new Response(200, [], $this->renderer->render('admin.html.twig', ['user' => $this->user]));
    }

    // display the form to create a new Blog Post
    public function createPost(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        return new Response(200, [], $this->renderer->render('create-post.html.twig'));
    }
    
    // send the request to Database
    public function addPost(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $user = $this->userManager->read($this->session->get('userID'));
        $title =  $this->request->getParsedBody()['title'];
        $content =  $this->request->getParsedBody()['content'];
        $slug =  $this->request->getParsedBody()['slug'];
        $leadSentence = $this->request->getParsedBody()['leadSentence'];
        $author = $user->getId();
        $newBlogPostId = $this->postManager->create($title, $content, $author, $slug, $leadSentence);
        // TODO: next step 3 : sécurité (htmlspecialchars etc)
        $this->postManager->read($newBlogPostId);
        // redirect to Admin Blog Posts List
        return new Response(301, ['Location' => 'show-posts']);
    }

    public function showPosts(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $posts = $this->postManager->readAll();
        foreach ($posts as $post) {
            $authorID = $post->getCreated_By();
            $author = $this->userManager->read($authorID);
            $authors[$authorID] = $author;
        }
        return new Response(
            200,
            [],
            $this->renderer->render(
                'admin-posts.html.twig',
                [
                                'posts' => $posts,
                                'authors' => $authors
                                ]
            )
        );
    }

    // form to edit a blog post
    public function editPost($id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $post = $this->postManager->read($id);
        return new Response(200, [], $this->renderer->render('edit-post.html.twig', ['post' => $post]));
    }

    public function updatePost($id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $title =  $this->request->getParsedBody()['title'];
        $content =  $this->request->getParsedBody()['content'];
        $slug =  $this->request->getParsedBody()['slug'];
        $leadSentence = $this->request->getParsedBody()['leadSentence'];
        $this->postManager->update($id, $title, $content, $slug, $leadSentence);
        // TODO: next step 3 : sécurIté (htmlspecialchars etc)
        // redirect to Admin Blog Posts List
        return new Response(301, ['Location' => '/admin/show-posts']);
    }

    public function deletePost($id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $this->postManager->delete($id);
        // redirect to Admin Blog Posts List
        return new Response(302, ['Location' => '/admin/show-posts']);
    }



    public function showUsers(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if(!$this->userController->isLoggedIn()){
            return new Response(301, ['Location' => 'login']);
        }
        $users = $this->userManager->readAll();

        return new Response(200, [], $this->renderer->render('users.html.twig', ['users' => $users]));
    }

    // public function getRoles()
    // {
    //     return $this->userManager->getAllRoles();
    // }

    // public function assignedRoles($user, $roles)
    // {
    //     // if(str_contains($user->getRoles(), $r))
    //     //TODO:
    // }


}
