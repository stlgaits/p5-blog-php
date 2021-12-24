<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{

    /**
     * Mailer lib
     *
     * @var PHPMailer
     */
    private $mailer;

    
    private $host;


    public function __construct($mailer)
	{
        $this->mailer = new PHPMailer();
         //Server settings
         $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
         $this->mailer->isSMTP();                                            //Send using SMTP
         $this->mailer->Host       = 'smtp.estellegaits.com';                     //Set the SMTP server to send through
        //  $this->mailer->SMTPAuth   = true;                                   //Enable SMTP authentication
         $this->mailer->Username   = 'estelle@gaits.com';                     //SMTP username
         $this->mailer->Password   = 'ceciestunmotdepassequejedoismodifier';                               //SMTP password
         $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
         $this->mailer->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
	}

    public function sendMail(): void
    {
        try {
            //Recipients
            $this->mailer->setFrom('from@example.com', 'Mailer');
            $this->mailer->addAddress('joe@example.net', 'Joe User');     //Add a recipient
            $this->mailer->addAddress('ellen@example.com');               //Name is optional
            $this->mailer->addReplyTo('info@example.com', 'Information');
            $this->mailer->addCC('cc@example.com');
            $this->mailer->addBCC('bcc@example.com');
        
            //Attachments
            $this->mailer->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            $this->mailer->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
        
            //Content
            $this->mailer->isHTML(true);                                  //Set email format to HTML
            $this->mailer->Subject = 'Here is the subject';
            $this->mailer->Body    = 'This is the HTML message body <b>in bold!</b>';
            $this->mailer->AltBody = 'This is the body in plain text for non-HTML mail clients';
        
            $this->mailer->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}";
        }
    }
}
