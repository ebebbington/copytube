<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Usage
 * $Mail = new Mail(params);
 * $Mail->send();
 */
class Mail
{
    use Queueable, SerializesModels;

    public $to = '';

    private $senderEmail = 'ebebbington.copytube@gmail.com';

    private $senderPassword = 'CopyTube1';

    private $senderName = 'CopyTube';

    private $title = '';

    private $message = '';

    private $name = '';

    private $PHPMailer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($to, $name, $title, $message)
    {
        $this->title = $title;
        $this->to = $to;
        $this->message = $message;
        $this->name = $name;
        try {
            $this->PHPMailer = new PHPMailer;
            $this->PHPMailer->isSMTP(); // use SMTP
            $this->PHPMailer->SMTPDebug = 2; // Allow debugging
            $this->PHPMailer->Host = 'smtp.gmail.com'; // gmail host
            $this->PHPMailer->SMTPAuth = true; // allow SMTP auth to pass in email and password
            $this->PHPMailer->Username = $this->senderEmail; // gmail email
            $this->PHPMailer->Password = $this->senderPassword; // gmail password
            $this->PHPMailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $this->PHPMailer->Port = 587; // self explantory
      
            // Setup the final email
            $this->PHPMailer->setFrom($this->senderEmail, $this->senderName);
            $this->PHPMailer->addAddress($this->to, $this->name);
            $this->PHPMailer->Subject = $this->title;
            $this->PHPMailer->Body = $this->message;
          } catch (Exception $e) {
            trigger_error('Error when constructing the PHPMailer class inside the Mail class : ' . $e->getMessage());
          }
    }

    public function send()
    {
    /* 
        * DONT REMOVE THIS CONDITIONAL.
        * Removing it will cause n infinite loop of emails. Read below
        *
        * When the class errors with no password, it calls the customer
        * error handler... which calls this class... which would
        * then fail... ending up in an infinite loop.
        * Luckily this didnt happen and i realised this before it happened.
        */
        if (!$this->PHPMailer->Password) {
            return false;
        }
        // Send
        if(!$this->PHPMailer->send()) {
            trigger_error('Email could not be sent. Most likely couldnt authenticate');
        }
    }
}
