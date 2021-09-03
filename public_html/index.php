<?php 

require_once realpath("./gaitsblog/vendor/autoload.php");

$app = new App\App([
    BlogModule::class
]);


$response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());

// using http-interpo/response-sender, we convert the PSR-7 response into HTTP output
Http\Response\send($response);
