<?php

use GuzzleHttp\Psr7\Response;

require_once realpath("./../vendor/autoload.php");
/**
 * For security reasons, store sensitive data in a .env file
 * This file must be located at the root of your project (same directory as .env.example)
 */
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
/** These variables MUST be filled in (in .env) for the blog to work */
$dotenv->required([
    'DB_HOST', 
    'DB_NAME', 
    'DB_USER', 
    'DB_PASSWD', 
    'BLOG_ADMIN_EMAIL', 
    'BLOG_ADMIN_FULLNAME'
]);

require_once __DIR__ . '/../src/config.php';

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();

/** Router : add list of routes with method, uri & handler */
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'HomeController@index');
    $r->addRoute('GET', '/home', 'HomeController@index');
    $r->addRoute('POST', '/send-mail', 'MailController@sendMail');
    $r->addRoute('GET', '/index', 'HomeController@index');
    $r->addRoute('GET', '/blog', 'BlogController@list');
    $r->addRoute('GET', '/login', 'UserController@login');
    $r->addRoute('GET', '/register', 'UserController@register');
    $r->addRoute('GET', '/profile', 'UserController@profile');
    $r->addRoute('POST', '/login-user', 'UserController@loginUser');
    $r->addRoute('POST', '/register-user', 'UserController@registerUser');
    $r->addRoute('POST', '/logout-user', 'UserController@logoutUser');
    $r->addRoute('GET', '/post/{id}', 'BlogController@post');
    $r->addRoute('POST', '/post/{postId}/add-comment', 'CommentController@addComment');
    $r->addRoute('GET', '/admin', 'AdminController@index');
    $r->addRoute('GET', '/admin/create-post', 'AdminController@createPost');
    $r->addRoute('GET', '/admin/edit-post/{id}', 'AdminController@editPost');
    $r->addRoute('POST', '/admin/update-post/{id}', 'AdminController@updatePost');
    $r->addRoute('POST', '/admin/add-post', 'AdminController@addPost');
    $r->addRoute('GET', '/admin/delete-post/{id}', 'AdminController@deletePost');
    $r->addRoute('GET', '/admin/show-posts', 'AdminController@showPosts');
    $r->addRoute('GET', '/admin/show-users', 'AdminController@showUsers');
    $r->addRoute('GET', '/admin/show-comments', 'AdminController@showPendingComments');
    $r->addRoute('POST', '/admin/approve-comment/{id}', 'CommentController@approveComment');
    $r->addRoute('POST', '/admin/reject-comment/{id}', 'CommentController@rejectComment');
    $r->addRoute('GET', '/admin/delete-user/{id}', 'AdminController@deleteUser');
    $r->addRoute('GET', '/admin/user/{id}', 'AdminController@editUser');
    
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
