<?php

namespace App\Controller;

use Exception;
use App\TwigRenderer;
use App\Model\PostManager;
use GuzzleHttp\Psr7\Response;
use App\Model\UserManager;

class BlogController
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
     * Post manager : PDO connection to Posts stored in the database
     *
     * @var Manager
     */
    private $manager;

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
        $this->manager = new PostManager();
        $this->userManager = new UserManager();
    }
    
    /**
     * Display all blog posts
     *
     * @return Response
     */
    public function list(): Response
    {
        $blogPosts = $this->manager->readAll();
        foreach ($blogPosts as $post){
            $authorID = $post->getCreated_By();
            $author = $this->userManager->read($authorID);
            $authors[$authorID] = $author;
        }

        return new Response(
            200, 
            [], 
            $this->renderer->render(
                'blog.html.twig', 
                [
                    'posts' => $blogPosts,
                    'authors' => $authors
                ]
            )
        );
    }

    /**
     * Display a single psot
     *
     * @return Response
     */
    public function post($id): Response
    {

        $post = $this->manager->read($id);
        $author = $this->userManager->read($post->getCreated_By());

        return new Response(
            200,
            [], 
            $this->renderer->render(
            'post.html.twig',
            [
                'post' => $post,
                'author' => $author
            ]
        ));
    }

    public function test(): Response
    {
        return new Response(200, [], $this->renderer->render('base.html.twig'));
    }
}
