<?php

use GuzzleHttp\Psr7\Response;

require_once realpath("./../vendor/autoload.php");
require_once __DIR__ . '/../src/config.php';

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();

/** Router : add list of routes with method, uri & handler */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'HomeController@index');
    $r->addRoute('GET', '/home', 'HomeController@index');
    $r->addRoute('GET', '/index', 'HomeController@index');
    $r->addRoute('GET', '/blog', 'BlogController@list');
    $r->addRoute('GET', '/login', 'UserController@login');
    $r->addRoute('GET', '/register', 'UserController@register');
    $r->addRoute('POST', '/login-user', 'UserController@loginUser');
    $r->addRoute('POST', '/register-user', 'UserController@registerUser');
    $r->addRoute('POST', '/logout-user', 'UserController@logoutUser');
    $r->addRoute('GET', '/post/{id}', 'BlogController@post');
    $r->addRoute('GET', '/admin', 'AdminController@index');
    $r->addRoute('GET', '/admin/create-post', 'AdminController@createPost');
    $r->addRoute('GET', '/admin/edit-post/{id}', 'AdminController@editPost');
    $r->addRoute('POST', '/admin/update-post/{id}', 'AdminController@updatePost');
    $r->addRoute('POST', '/admin/add-post', 'AdminController@addPost');
    $r->addRoute('GET', '/admin/delete-post/{id}', 'AdminController@deletePost');
    $r->addRoute('GET', '/admin/show-posts', 'AdminController@showPosts');
    $r->addRoute('GET', '/admin/show-users', 'AdminController@showUsers');
    $r->addRoute('GET', '/admin/show-comments', 'AdminController@showPendingComments');
    $r->addRoute('POST', '/admin/approve-comment/{id}', 'AdminController@approveComment');
    $r->addRoute('POST', '/admin/reject-comment/{id}', 'AdminController@rejectComment');
    $r->addRoute('GET', '/admin/delete-user/{id}', 'AdminController@deleteUser');
});

// Fetch method and URI from Server Globals
$httpMethod = $request->getServerParams()['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}

$uri = rawurldecode($uri);

// Ignore potential trailing slash from URI (except on root URL)
if (!empty($uri) && $uri[-1] === "/" && strlen($uri) > 1) {
    $uri = substr($uri, 0, -1);
    $response = new Response(301, ['Location' => $uri]);
}

$response = new Response();

try {
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    if (!empty($routeInfo[1]) && is_string($routeInfo[1])) {
        $className = substr($routeInfo[1], 0, strpos($routeInfo[1], '@'));
        $methodName = substr($routeInfo[1], strpos($routeInfo[1], '@') + 1);
    }
} catch (Exception $e) {
    //TODO: il faut que ce soit plus propre que ça
    var_dump($e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile());
    throw new Exception('La classe ou la méthode demandée n\'est pas reconnue');
}


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
