<?php

namespace App\Controller;

use App\Auth;
use App\Model\PostManager;
use App\Model\UserManager;
use App\Model\CommentManager;
use GuzzleHttp\Psr7\Response;

class BlogController extends DefaultController
{
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
    
    /**
     * Comment manager : PDO connection to Comments stored in the database
     *
     * @var CommentManager
     */
    private $commentManager;


    /**
     * User auth / guard
     *
     * @var Auth
     */
    private $userAuth;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new PostManager();
        $this->userManager = new UserManager();
        $this->commentManager = new CommentManager();
        $this->userAuth = new Auth();
    }
    
    /**
     * Displays all blog posts
     *
     * @return Response
     */
    public function list(): Response
    {

        $blogPosts = $this->manager->readAll();
        foreach ($blogPosts as $post) {
            $authorID = $post->getCreated_By();
            $author = $this->userManager->read($authorID);
            $authors[$authorID] = $author;
        }
        if (!$this->userAuth->isLoggedIn()) {
            return new Response(
                200,
                [],
                $this->renderer->render(
                    'blog.html.twig',
                    [
                        'posts' => $blogPosts,
                        'authors' => $authors,
                    ]
                )
            );
        }
        $user = $this->userAuth->getCurrentUser();
        return new Response(
            200,
            [],
            $this->renderer->render(
                'blog.html.twig',
                [
                    'posts' => $blogPosts,
                    'authors' => $authors,
                    'user' => $user
                ]
            )
        );
    }

    /**
     * Display a single post
     *
     * @return Response
     */
    public function post(int $id): Response
    {
        $post = $this->manager->read($id);
        $author = $this->userManager->read($post->getCreated_By());
        $comments = $this->commentManager->getApprovedComments($id);
        $message = $this->session->get('message');

        if (!$this->userAuth->isLoggedIn()) {
            return new Response(
                200,
                [],
                $this->renderer->render(
                    'post.html.twig',
                    [
                        'post' => $post,
                        'author' => $author,
                        'comments' => $comments
                    ]
                )
            );
        }
        $user = $this->userAuth->getCurrentUser();
        return new Response(
            200,
            [],
            $this->renderer->render(
                'post.html.twig',
                [
                'post' => $post,
                'author' => $author,
                'user' => $user,
                'comments' => $comments,
                'message' => $message
                ]
            )
        );
    }
}
