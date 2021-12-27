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


    public function __construct()
	{
        // passing true enables exceptions
        $this->mailer = new PHPMailer(true);
         // Server settings
         $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
         $this->mailer->isSMTP();                                            //Send using SMTP
         $this->mailer->Host       = __SMTPHOST;                     //Set the SMTP server to send through
         $this->mailer->SMTPAuth   = true;                                   //Enable SMTP authentication
         $this->mailer->Username   = __SMTPUSERNAME;                     //SMTP username
         $this->mailer->Password   = __SMTPPASSWD;                               //SMTP password
         $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
         $this->mailer->Port       = __SMTPPORT;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
	}

    public function sendMail(string $subject, string $body, string $fromAddress, string $fromName): string
    {
        try {
            // Recipients : blog owner (the contact form must send messages to blog admin)
            $this->mailer->setFrom($fromAddress, $fromName);
            $this->mailer->addAddress(__SMTPEMAILADDRESS, __SMTPFULLNAME);     //Add a recipient
            $this->mailer->addAddress(__SMTPEMAILADDRESS2);               //Name is optional
            $this->mailer->addReplyTo($fromAddress, $fromName);

            // Content
            $this->mailer->isHTML(true);                                  //Set email format to HTML
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;
            $this->mailer->AltBody = $body;
            $mail =  $this->mailer->send();
            if (!$mail) {
                    return $this->mailer->ErrorInfo;
            } else{
                    return 'Message bien envoyé';
            }
        } catch (Exception $e) {
            return "Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo} - PHP Error : {$e->getMessage()} line {$e->getLine()} at {$e->getFile()}";
        }
    }
}
