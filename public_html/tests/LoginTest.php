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
        // $this->renderer = new TwigRenderer();
    }

    public function test_empty_email_fails()
    {
        $response = $this->controller->login();
        $this->assertEquals(200, $response->getStatusCode());
        // var_dump($response);
        // die();
    }
}
