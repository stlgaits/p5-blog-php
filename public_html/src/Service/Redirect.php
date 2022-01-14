<?php

namespace App\Service;

use GuzzleHttp\Psr7\Response;

class Redirect
{
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
        return $this->redirect('admin/show-posts');
    }   
    
    public function redirectToBlog()
    {
        return $this->redirect('blog');
    }

}