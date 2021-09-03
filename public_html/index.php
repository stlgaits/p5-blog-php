<?php 

// $host = 'mysql';
// $user = 'root';
// $pass = 'root';
// $conn = new mysqli($host, $user, $pass);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// } 
// echo "Connected to MySQL successfully!";


require_once realpath("./blog/vendor/autoload.php");

$app = new App\App();
$response = $app->run(GuzzleHttp\Psr7\ServerRequest::fromGlobals());

// using http-interpo/response-sender, we convert the PSR-7 response into HTTP output
Http\Response\send($response);
