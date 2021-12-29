<?php

namespace App\Controller;

use App\Mailer;
use App\Session;
use App\TwigRenderer;
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

    /**
     * Controller used to redirect to homepage once form is submitted
     *
     * @var HomeController
     */
    private $homeController;
    
    public function __construct()
    {
        parent::__construct();
        $this->homeController = new HomeController();
        $this->mailer = new Mailer();
    }

    public function sendMail()
    {
        $emailAddress = filter_var($this->request->getParsedBody()['emailAddress'], FILTER_VALIDATE_EMAIL);
        $firstname = filter_var($this->request->getParsedBody()['firstname'], FILTER_SANITIZE_STRING);
        $lastname = filter_var($this->request->getParsedBody()['lastname'], FILTER_SANITIZE_STRING);
        $message = filter_var($this->request->getParsedBody()['message'], FILTER_SANITIZE_STRING);
        // Send email to mailer 
        $mail = $this->mailer->sendMail("Contact - Blog PHP Estelle Gaits", $message, $emailAddress, $firstname.' '.$lastname);
        // $flashMessage = "Votre message a été correctement envoyé";
        $flashMessage = $mail;
        // $this->session->set('flashMessage', $flashMessage);
        // return new Response(200, [], $this->renderer->render('home.html.twig', ['flashMessage' => $flashMessage]));
        return $this->homeController->index();
    }
}