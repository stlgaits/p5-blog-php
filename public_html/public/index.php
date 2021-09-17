<?php 

use Mezzio\Router\Route;
use GuzzleHttp\Psr7\Response;
use Mezzio\Router\RouteCollector;
use Mezzio\Router\FastRouteRouter;
use Mezzio\Router\Middleware\DispatchMiddleware;

require_once realpath("./../vendor/autoload.php");

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();


$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/home', 'HomeController@hello');
    $r->addRoute('GET', '/blog', 'BlogController@index');
    $r->addRoute('GET', '/test', 'BlogController@test');
    $r->addRoute('GET', '/michel', 'BlogController@michel');
});

// Fetch method and URI from somewhere
$httpMethod = $request->getServerParams()['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$className = substr($routeInfo[1], 0, strpos($routeInfo[1], '@'));
$methodName = substr($routeInfo[1], strpos($routeInfo[1], '@') +1);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $class = 'App\Controller\\' . $className;
        $controller = new $class;
        $handler = array($controller, $methodName);
        $vars = $routeInfo[2];
        call_user_func_array($handler, $vars);
        break;
}

