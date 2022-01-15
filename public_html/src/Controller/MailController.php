<?php

namespace App\Controller;

use Exception;
use App\Mailer;
use GuzzleHttp\Psr7\Response;
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

    public function sendMail(): Response
    {
        try {
            $flashMessage = "Désolée, votre message n'a pas pu être envoyé.";
            // input from the Contact form
            $emailAddress = filter_var($this->request->getParsedBody()['emailAddress'], FILTER_VALIDATE_EMAIL);
            $firstname = filter_var($this->request->getParsedBody()['firstname'], FILTER_SANITIZE_STRING);
            $lastname = filter_var($this->request->getParsedBody()['lastname'], FILTER_SANITIZE_STRING);
            $message = filter_var($this->request->getParsedBody()['message'], FILTER_SANITIZE_STRING);
            if ($emailAddress === false || $firstname === false || $lastname === false || $message === false){
                return $this->redirect->redirectToHomePage();
            }
            // Send email to mailer 
            $mail = $this->mailer->sendMail("Contact - Blog PHP Estelle Gaits", $message, $emailAddress, $firstname.' '.$lastname);
            if($mail === 1){
                $flashMessage = "Votre message a bien été envoyé. Un administrateur vous répondra par email.";
            }
        } catch (Exception $e){
            $flashMessage = $e->getMessage();
        }
        // Store flash message(response from mailer) in user session
        $this->session->set('flashMessage', $flashMessage);
        return $this->redirect->redirectToHomePage();
    }
}