<?php

namespace App\Controller;

use App\Mailer;
use GuzzleHttp\Psr7\Response;
use App\Controller\HomeController;
use App\Controller\DefaultController;

class MailController extends DefaultController
{

    /**
     * Mailer that handles emails via SMTP 
     *
     * @var Mailer
     */
    private $mailer;
    
    public function __construct()
    {
        parent::__construct();
        $this->mailer = new Mailer();
    }

    public function sendMail()
    {
        // input from the Contact form
        $emailAddress = filter_var($this->request->getParsedBody()['emailAddress'], FILTER_VALIDATE_EMAIL);
        $firstname = filter_var($this->request->getParsedBody()['firstname'], FILTER_SANITIZE_STRING);
        $lastname = filter_var($this->request->getParsedBody()['lastname'], FILTER_SANITIZE_STRING);
        $message = filter_var($this->request->getParsedBody()['message'], FILTER_SANITIZE_STRING);
        // Send email to mailer 
        $mail = $this->mailer->sendMail("Contact - Blog PHP Estelle Gaits", $message, $emailAddress, $firstname.' '.$lastname);
        $flashMessage = "Désolée, votre message n'a pas pu être envoyé.";
        if($mail === 1){
            $flashMessage = "Votre message a bien été envoyé. Un administrateur vous répondra par email.";
        }
        // Store flash message(response from mailer) in user session
        $this->session->set('flashMessage', $flashMessage);
        return new Response(301, ['Location' => '/'] );
    }
}