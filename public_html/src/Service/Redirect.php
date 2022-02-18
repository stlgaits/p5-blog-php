<?php

namespace App\Service;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;

/**
 * Centralizes redirect responses
 */
class Redirect
{
    /**
     *
     * @var ServerRequest
     */
    protected $request;

    public function __construct()
    {
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }

    /**
     * @param  string $location
     * @return Response
     */
    private function redirect(string $location): Response
    {
        return new Response(302, ['Location' => $location]);
    }

    public function redirectToHomePage()
    {
        return $this->redirect('/');
    }

    public function redirectToLoginPage()
    {
        return $this->redirect('/login');
    }

    public function redirectToProfilePage()
    {
        return $this->redirect('/profile');
    }

    public function redirectToAdminBlogPostsList()
    {
        return $this->redirect('/admin/show-posts');
    }
    
    public function redirectToAdminCommentsList()
    {
        return $this->redirect('/admin/show-comments');
    }

    public function redirectToAdminUsersList()
    {
        return $this->redirect('/admin/show-users');
    }

    public function redirectToBlog()
    {
        return $this->redirect('/blog');
    }

    public function redirectToCurrentBlogPost(int $id)
    {
        return $this->redirect("/post/$id");
    }

    public function redirectToPreviousPage()
    {
        $previousPageUrl = $this->request->getServerParams()['HTTP_REFERER'];
        $originUrl = $this->request->getServerParams()['HTTP_ORIGIN'];
        $previousUri = str_replace($originUrl, '', $previousPageUrl);
        return $this->redirect($previousUri);
    }
}
