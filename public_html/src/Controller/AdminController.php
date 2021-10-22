<?php

namespace App\Controller;

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
     * @var UsertManager
     */
    private $userManager;
    
    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
    }

    public function index(): Response
    {
        return new Response(200, [], $this->renderer->render('admin.html.twig'));
    }

    // display the form to create a new Blog Post
    public function createPost(): Response
    {
        return new Response(200, [], $this->renderer->render('create-post.html.twig'));
    }
    
    // send the request to Database
    public function addPost(): Response
    {
        if (isset($_POST)) {
            // next step 1 : récupérer le User proprement (session?) & le passer en param
            // next step 2 : éviter la variable super globale
            // next step 3 : sécurité (htmlspecialchars etc)
            $newBlogPostId = $this->postManager->create($_POST['title'], $_POST['content'], 2, $_POST['slug']);
            $this->postManager->read($newBlogPostId);
        }
        // redirect to Admin Blog Posts List
        return new Response(301, ['Location' => 'show-posts']);
    }

    public function showPosts(): Response
    {
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
        $post = $this->postManager->read($id);
        return new Response(200, [], $this->renderer->render('edit-post.html.twig', ['post' => $post]));
    }

    public function updatePost($id): Response
    {
        if (isset($_POST)) {
            // next step 1 : récupérer le User proprement (session?) & le passer en param
            // next step 2 : éviter la variable super globale
            // next step 3 : sécurité (htmlspecialchars etc)
            $updatedBlogPostId = $this->postManager->update($id, $_POST['title'], $_POST['content'], $_POST['slug']);
        }
        // redirect to Admin Blog Posts List
        return new Response(301, ['Location' => '/admin/show-posts']);
    }

    public function deletePost($id): Response
    {
        $this->postManager->delete($id);
        // redirect to Admin Blog Posts List
        return new Response(302, ['Location' => '/admin/show-posts']);
    }



    public function showUsers(): Response
    {
        $users = $this->userManager->readAll();
        $roles = $this->getRoles();
        var_dump($users);
        die();
        return new Response(200, [], $this->renderer->render('users.html.twig', ['users' => $users]));
    }

    public function getRoles()
    {
        return $this->userManager->getAllRoles();
    }

    public function assignedRoles($user, $roles)
    {
        // if(str_contains($user->getRoles(), $r))
        //TODO:
    }
}
