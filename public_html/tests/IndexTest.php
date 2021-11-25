<?php

namespace Tests;

use App\TwigRenderer;
use App\Model\UserManager;
use PHPUnit\Framework\TestCase;
use App\Controller\HomeController;

class IndexTest extends TestCase
{


    protected $controller;
    protected $userManager;

    public function setUp(): void
    {
        define('__DBHOST', 'localhost');
        define('__DBNAME', 'blog');
        define('__DBUSER', 'root');
        define('__DBPASSWD', 'root');
        $this->userManager = new UserManager();
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
