<?php

namespace Tests;

use App\TwigRenderer;
use PHPUnit\Framework\TestCase;
use App\Controller\HomeController;

class IndexTest extends TestCase
{


    protected $controller;

    public function setUp(): void
    {
        $this->controller = new HomeController();
        $this->renderer = new TwigRenderer();
    }

    public function test_homepage_is_displayed(): void
    {
        $response = $this->controller->index();

        // $this->assertEquals("<h1>SALUT</h1>", $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}
