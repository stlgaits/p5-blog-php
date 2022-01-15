<?php

namespace App\Service;

use GuzzleHttp\Psr7\Response;

/**
 * Centralizes redirect responses
 */
class Redirect
{
    /**
     * @param string $location
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
        return $this->redirect('login');
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
        return $this->redirect('blog');
    }

    public function redirectToCurrentBlogPost(int $id)
    {
        return $this->redirect("/post/$id");
    }
}
