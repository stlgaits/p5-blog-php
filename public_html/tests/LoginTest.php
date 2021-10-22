<?php

namespace Tests;

use App\Controller\UserController;
use App\TwigRenderer;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{


    protected $controller;

    public function setUp(): void
    {
        $this->controller = new UserController();
        $this->renderer = new TwigRenderer();
    }

    public function test_empty_email_fails()
    {
        $response = $this->controller->loginUser();
        // var_dump($response);
        // die();
    }

    // public function test_homepage_is_displayed(): void
    // {
    //     $response = $this->controller->index();
    //     $this->assertEquals("<h1>SALUT</h1>", $response->getBody());
    //     $this->assertEquals(200, $response->getStatusCode());
    // }
}
