<?php

namespace Tests\Framework\Modules;

use App\Framework\Router;

class ErroredModule
{

    public function __construct(Router $router)
    {

        $router->get('/demo', function () {
            return new \stdClass();
        }, 'demo');
    }
}
