<?php

namespace App\Controller;

use App\Auth;
use App\Entity\Comment;
use App\Model\CommentManager;
use GuzzleHttp\Psr7\Response;

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
            return $this->redirect->redirectToLoginPage();
        }
        $user = $this->userAuth->getCurrentUser();
        $author = $user->getId();
        $title = htmlspecialchars($this->request->getParsedBody()['commentTitle']);
        $content = htmlspecialchars($this->request->getParsedBody()['commentContent']);
        $newCommentId = $this->commentManager->create($title, $content, $author, $postId);
        // TODO: next step 3 : sécurité (htmlspecialchars etc)
        $this->commentManager->read($newCommentId);
        $flashMessage = 'Votre commentaire a bien été ajouté et est en attente de validation par un administrateur';
        $this->session->set('flashMessage', $flashMessage);

        return $this->redirect->redirectToCurrentBlogPost($postId);
    }

    public function rejectComment(int $id): Response
    {
        return $this->decideOnCommentStatus($id, 'reject');
    }

    public function approveComment(int $id): Response
    {
        return $this->decideOnCommentStatus($id, 'approve');
    }

    public function reject(Comment $comment)
    {
        return $comment->setStatus($comment::REJECTED);
    }

    public function approve(Comment $comment)
    {
        return $comment->setStatus($comment::APPROVED);
    }

    public function decideOnCommentStatus(int $id, string $action): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $comment = $this->commentManager->read($id);
        $status = $comment->getStatus();
        // Cannot reject a comment that's not pending
        if ($status !== $comment::PENDING) {
            return $this->redirect->redirectToAdminCommentsList();
        }
        if ($action === 'approve') {
            $this->approve($comment);
        } else {
            $this->reject($comment);
        }
        $this->commentManager->updateCommentStatus($id, $comment->getStatus());
        return $this->redirect->redirectToAdminCommentsList();
    }
}
