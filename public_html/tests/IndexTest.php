<?php

namespace Tests;

use App\Controller\HomeController;
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase {


    protected $controller;

    public function setUp(): void {
        $this->controller = new HomeController;
    }

    public function test_homepage_is_displayed(): void {
        $response = $this->controller->index();
        $this->assertEquals("<h1>SALUT</h1>", $response->getBody());
        $this->assertEquals(200, $response->getStatusCode());
    }
}