<?php

use GuzzleHttp\Psr7\Response;

require_once realpath("./../vendor/autoload.php");

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();

/** Router : add list of routes with method, uri & handler */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'HomeController@index');
    $r->addRoute('GET', '/home', 'HomeController@index');
    $r->addRoute('GET', '/index', 'HomeController@index');
    $r->addRoute('GET', '/blog', 'BlogController@list');
    $r->addRoute('GET', '/test', 'BlogController@test');
    $r->addRoute('GET', '/post', 'BlogController@post');
});

// Fetch method and URI from Server Globals
$httpMethod = $request->getServerParams()['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

try {
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    if (!empty($routeInfo[1])) {
        $className = substr($routeInfo[1], 0, strpos($routeInfo[1], '@'));
        $methodName = substr($routeInfo[1], strpos($routeInfo[1], '@') + 1);
    }
} catch (Exception $e) {
    var_dump($e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
    throw new Exception('La classe ou la méthode demandée n\'est pas reconnue');
}
$response = new Response();

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        $class = 'App\Controller\\NotFoundController';
        $methodName = 'notFound';
        $controller = new $class();
        $vars = [];
        $handler = array($controller, $methodName);
        $response = call_user_func_array($handler, $vars);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $class = 'App\Controller\\' . $className;
        $controller = new $class();
        $handler = array($controller, $methodName);
        $vars = $routeInfo[2];
        // send response
        $response = call_user_func_array($handler, $vars);
        break;
}

Http\Response\send($response);
