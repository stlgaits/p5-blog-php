<?php

namespace App\Controller;

use App\Mailer;
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
        $this->mailer = new Mailer();
        $this->session = new Session();
        $this->request =  \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
    }

    public function sendMail(): Response
    {
        $emailAddress =  filter_var($this->request->getParsedBody()['emailAddress'], FILTER_VALIDATE_EMAIL);
        $firstname = filter_var($this->request->getParsedBody()['firstname'], FILTER_SANITIZE_STRING);
        $lastname = filter_var($this->request->getParsedBody()['lastname'], FILTER_SANITIZE_STRING);
        $message =  filter_var($this->request->getParsedBody()['message'], FILTER_SANITIZE_STRING);
        // Send email to mailer 
        $mail = $this->mailer->sendMail("Contact - Blog PHP Estelle Gaits", $message, $emailAddress, $firstname.' '.$lastname);
        $flashMessage = "Votre message a été correctement envoyé";
        // $this->session->set('flashMessage', $flashMessage);
        // return new Response(301, ['Location' => '/']);
        return new Response(200, [], $this->renderer->render('home.html.twig'));
    }
}