<?php

namespace App\Controller;

use App\Auth;
use App\Model\CommentManager;
use GuzzleHttp\Psr7\Response;

//TODO: refactor the code in ADMINCONTROLLER to implement this class (and do the same for Post & User as well)
class CommentController extends DefaultController
{

    /**
     * Comments PDO connection to database
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
        $this->userAuth = new Auth();
        $this->commentManager = new CommentManager();
    }


    public function addComment(int $postId): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return new Response(301, ['Location' => 'login']);
        }
        $user = $this->userAuth->getCurrentUser();
        $author = $user->getId();
        $title =  $this->request->getParsedBody()['commentTitle'];
        $content =  $this->request->getParsedBody()['commentContent'];
        $newCommentId = $this->commentManager->create($title, $content, $author, $postId);
        // TODO: next step 3 : sécurité (htmlspecialchars etc)
        $this->commentManager->read($newCommentId);
        $message = 'Votre commentaire a bien été ajouté et est en attente de validation par un administrateur';
        $_SESSION['message'] = $message;
        // TODO: find out how to pass the message to the view within a redirect
        return new Response(301, ['Location' => 'post/'.$postId]);
    }

}