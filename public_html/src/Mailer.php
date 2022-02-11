<?php

namespace App;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

/**
 * Allows you to send emails via SMTP using the PHPMailer library
 * The config variables relative to your SMTP must be stored in .env file
 */
class Mailer
{

    /**
     * Mailer lib
     *
     * @var PHPMailer
     */
    private $mailer;

    public function __construct()
    {
        // passing true enables exceptions
        $this->mailer = new PHPMailer(true);
         // Server settings
        //  $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;   // Enable verbose debug output (for dev only)
        $this->mailer->isSMTP();                            // Send using SMTP
         $this->mailer->Host       = __SMTPHOST;           // Set the SMTP server to send through
         $this->mailer->SMTPAuth   = true;                // Enable SMTP authentication
         $this->mailer->Username   = __SMTPUSERNAME;     // SMTP username
         $this->mailer->Password   = __SMTPPASSWD;      // SMTP password
         $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // Enable implicit TLS encryption
         $this->mailer->Port       = __SMTPPORT;      // TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    }

    /**
     * Sends emails from a contact form to the blog admin's email via SMTP
     *
     * @param  string $subject     = the email's title
     * @param  string $body        = the user's message submitted via the form
     * @param  string $fromAddress = the address the user supplied in the form input
     * @param  string $fromName    = first name & last name the user supplied when submitting the form
     * @return string
     */
    public function sendMail(string $subject, string $body, string $fromAddress, string $fromName): string
    {
        try {
            // Recipients : blog owner (the contact form must send messages to blog admin)
            $this->mailer->setFrom($fromAddress, $fromName);
            $this->mailer->addAddress(__SMTPEMAILADDRESS, __SMTPFULLNAME); // Add a recipient
            $this->mailer->addAddress(__SMTPEMAILADDRESS2);               // Name is optional
            $this->mailer->addReplyTo($fromAddress, $fromName);

            // Content
            $this->mailer->isHTML(true);       // Set email format to HTML
            $this->mailer->Subject = $subject;  // Email title
            $this->mailer->Body    = $body;    // Message content
            $this->mailer->AltBody = $body;
            $mail =  $this->mailer->send();
            if (!$mail) {
                    return $this->mailer->ErrorInfo;
            } else {
                    return $mail;
            }
        } catch (Exception $e) {
            return "Le message n'a pas pu être envoyé. Mailer Error: {$this->mailer->ErrorInfo} - PHP Error : {$e->getMessage()} line {$e->getLine()} at {$e->getFile()}";
        }
    }
}
