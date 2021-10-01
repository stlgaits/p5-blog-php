<?php

namespace App\Controller;

use Exception;
use App\Entity\Post;
use App\TwigRenderer;
use App\Model\PostManager;
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
    
    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->postManager = new PostManager();
    }

    public function index(): Response
    {
        return new Response(200, [], $this->renderer->render('admin.html.twig'));
    }

    public function createPost(): Response
    {
        return new Response(200, [], $this->renderer->render('create-post.html.twig'));
    } 
    
    public function addPost(): Response
    {
        if(isset($_POST)){
            // next step 1 : récupérer le User proprement (session?) & le passer en param
            // next step 2 : éviter la variable super globale 
            // next step 3 : sécurité (htmlspecialchars etc) 
            $newBlogPostId = $this->postManager->create($_POST['title'], $_POST['content'], 2, $_POST['slug']);
        } 
        return new Response(200, [], $this->renderer->render('add-post.html.twig'));
    }
}