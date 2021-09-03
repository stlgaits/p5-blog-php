<?php

namespace Tests;

use App\App;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;

class AppTest extends TestCase {

    public function testRedirectTrailingSlash(){
        $app = new App();
        $request = new ServerRequest("GET", "/demoslash/");
        $response = $app->run($request);
        $this->assertContains('/demoslash', $response->getHeader('Location'));
        $this->assertEquals(301, $response->getStatusCode());
    }

    public function testBlog(){
        $app = new App();
        $request = new ServerRequest('GET', '/blog');
        $response = $app->run($request);
        // $this->assertContains('<h1>Bienvenue sur mon blog</h1>', $response->getBody());
        $this->assertContains('/blog', $response->getHeader('Location'));
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testError404(){
        $app = new App();
        $request = new ServerRequest('GET', '/zhuizh');
        $response = $app->run($request);
        // $this->assertContains('<h1>Erreur 404</h1>', $response->getBody());
        $this->assertContains('/blog', $response->getHeader('Location'));
        $this->assertEquals(404, $response->getStatusCode());
    }
}