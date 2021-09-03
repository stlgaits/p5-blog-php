<?php

namespace App\Controller;

class PostsController
{

    public function __construct()
    {
        // echo 'test';
    }

    public function show($id)
    {
        echo " Je suis l'article $id";
    }
}
