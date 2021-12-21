<?php

namespace App\Controller;

use App\TwigRenderer;
use App\Model\UserManager;
use App\Model\CommentManager;
use GuzzleHttp\Psr7\Response;
use App\Controller\UserController;

//TODO: refactor the code in ADMINCONTROLLER to implement this class (and do the same for Post & User as well)
class CommentController
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
     * User Controller
     *
     * @var UserController
     */
    private $userController;

    /**
     *
     * @var ServerRequest
     */
    private $request;

    /**
     * Comments PDO connection to database
     *
     * @var CommentManager
     */
    private $commentManager;

    public function __construct()
    {
        $this->environment = new TwigRenderer();
        $this->renderer = $this->environment->getTwig();
        $this->userController = new UserController();
        $this->commentManager = new CommentManager();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }


    public function addComment(int $postId): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userController->isLoggedIn()) {
            return new Response(301, ['Location' => '/../../login']);
        }
        $user = $this->userController->getCurrentUser();
        $author = $user->getId();
        $title =  $this->request->getParsedBody()['commentTitle'];
        $content =  $this->request->getParsedBody()['commentContent'];
        $newCommentId = $this->commentManager->create($title, $content, $author, $postId);
        // TODO: next step 3 : sécurité (htmlspecialchars etc)
        $this->commentManager->read($newCommentId);
        $message = 'Votre commentaire a bien été ajouté et est en attente de validation par un administrateur';
        $_SESSION['message'] = $message;
        // TODO: find out how to pass the message to the view within a redirect
        return new Response(301, ['Location' => './../../post/'.$postId]);
    }

}