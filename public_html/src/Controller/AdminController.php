<?php

namespace App\Controller;

use App\Auth;
use App\Model\PostManager;
use App\Model\UserManager;
use App\Model\CommentManager;
use GuzzleHttp\Psr7\Response;

class AdminController extends DefaultController
{
    /**
     * PostManager
     *
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
     * Currently logged in user
     *
     * @var User|boolean
     */
    private $user;

    /**
     * Comments
     *
     * @var CommentManager
     */
    private $commentManager;

    public function __construct()
    {
        parent::__construct();
        $this->postManager = new PostManager();
        $this->userManager = new UserManager();
        $this->commentManager = new CommentManager();
        $this->userAuth = new Auth();
        $this->user = $this->userAuth->getCurrentUser();
    }

    /**
     * Admin dashboard homepage
     *
     * @return Response
     */
    public function index(): Response
    {
        if ($this->userAuth->isCurrentUserAdmin() === false) {
            return $this->redirect->redirectToLoginPage();
        }
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        return new Response(200, [], $this->renderer->render('admin.html.twig', ['user' => $this->user]));
    }

    // display the form to create a new Blog Post
    public function createPost(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        return new Response(200, [], $this->renderer->render('create-post.html.twig', ['user' => $this->user]));
    }
    
    /**
     * Sends request to add a new blog post to database manager
     *
     * @return Response
     */
    public function addPost(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
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
        return $this->redirect->redirectToAdminBlogPostsList();
    }

    /**
     * Renders the list of blog posts in admin dashboard (as a table)
     *
     * @return Response
     */
    public function showPosts(): Response
    {
        $authors = [];
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
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
                                'authors' => $authors,
                                'user' => $this->user
                                ]
            )
        );
    }

    /**
     * Renders the view where admin can edit a blog post
     *
     * @param  integer $id
     * @return Response
     */
    public function editPost(int $id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $post = $this->postManager->read($id);
        return new Response(200, [], $this->renderer->render('edit-post.html.twig', ['post' => $post, 'user' => $this->user]));
    }

    /**
     * Update a blog post
     *
     * @param  integer $id
     * @return Response
     */
    public function updatePost(int $id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $title =  $this->request->getParsedBody()['title'];
        $content =  $this->request->getParsedBody()['content'];
        $slug =  $this->request->getParsedBody()['slug'];
        $leadSentence = $this->request->getParsedBody()['leadSentence'];
        $this->postManager->update($id, $title, $content, $slug, $leadSentence);
        // TODO: next step 3 : sécurIté (htmlspecialchars etc)
        return $this->redirect->redirectToAdminBlogPostsList();
    }

    /**
     * Deletes a blog post
     *
     * @param  integer $id
     * @return Response
     */
    public function deletePost(int $id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $this->postManager->delete($id);
        return $this->redirect->redirectToAdminBlogPostsList();
    }

    /**
     * Renders a list of users sorted into a table
     *
     * @return Response
     */
    public function showUsers(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $users = $this->userManager->readAll();

        return new Response(200, [], $this->renderer->render('users.html.twig', ['users' => $users,   'user' => $this->user]));
    }

    /**
     * Disables a user's account & privileges
     *
     * @param  int $id
     * @return Response
     */
    public function deleteUser(int $id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $this->userManager->disable($id);
        return $this->redirect->redirectToAdminUsersList();
    }
    
    /**
     * Renders the form to edit a user from Admin Dashboard
     *
     * @param  int $id
     * @return Response
     */
    public function editUser(int $id): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $user = $this->userManager->read($id);
        return new Response(200, [], $this->renderer->render('edit-user.html.twig', ['user' => $this->user, 'userAccount' => $user]));
    }


    //TODO: showUser() => show all info for a specific user

    public function showPendingComments(): Response
    {
        // only allow access to users who are both logged in and have admin role
        if (!$this->userAuth->isLoggedIn()) {
            return $this->redirect->redirectToLoginPage();
        }
        $comments = $this->commentManager->readAllWithAuthorsAndPostTitle();
        return new Response(200, [], $this->renderer->render('pending-comments.html.twig', ['comments' => $comments, 'user' => $this->user]));
    }
}
