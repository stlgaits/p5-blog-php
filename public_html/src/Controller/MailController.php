<?php

namespace App\Controller;

use App\Session;
use GuzzleHttp\Psr7\Response;

class MailController
{
    
    /**
     *
     * @var ServerRequest
     */
    private $request;
    
    public function __construct()
    {
        
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }

    public function sendMail(): Response
    {
        $emailAddress =  filter_var($this->request->getParsedBody()['emailAddress'], FILTER_VALIDATE_EMAIL);
        $firstname = filter_var($this->request->getParsedBody()['firstname'], FILTER_SANITIZE_STRING);
        $lastname = filter_var($this->request->getParsedBody()['lastname'], FILTER_SANITIZE_STRING);
        $message =  filter_var($this->request->getParsedBody()['message'], FILTER_SANITIZE_STRING);

        $flashMessage = "Votre message a été correctement envoyé";
        $this->session->set('flashMessage', $flashMessage);
        var_dump($message);
        die();
        return new Response(301, ['Location' => '/']);
    }
}