<?php

namespace App\Controller;

use App\Model\PostManager;
use App\TwigRenderer;
use GuzzleHttp\Psr7\Response;
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

    
    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->manager = new PostManager();
    }
    
    /**
     * Display all blog posts
     *
     * @return Response
     */
    public function list(): Response
    {
        $blogPosts = $this->manager->readAll();

        return new Response(200, [], 
            $this->renderer->render('blog.html.twig', 
                [
                'posts' => $blogPosts
                ]
            )
        );
    }

    /**
     * Display a single psot
     *
     * @return Response
     */
    public function post(): Response
    {
        return new Response(200, [], $this->renderer->render('post.html.twig'));
    }

    public function test(): Response
    {
        return new Response(200, [], $this->renderer->render('base.html.twig'));
    }
}
