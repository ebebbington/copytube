<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once '/usr/lib/vendor/autoload.php';

/**
 *  The Mail Model
 * 
 * Sets up PHPMailer and mail options such as sender, and sends an email when
 * requested
 * 
 * @author Edward Bebbington
 * @copyright
 * @license
 * @method __construct()
 * @method send()
 * 
 * Usage
 * $Mail = new Mail(params);
 * $Mail->send();
 */
class Mail {

  /////////////////////////////////////////////////////////
  // Class properties
  /////////////////////////////////////////////////////////
  /** @var String $senderEmail The email of the sender e.g me */
  private $senderEmail = '';
  /** @var String $senderName Name of the sender e.g CopyTube */
  private $senderName = '';
  /** @var String $receiverEmail Email of whom to send the email to */
  private $receiverEmail = '';
  /** @var String $receieverName Name of whom the email is being sent to */
  private $receieverName = '';
  /** @var String $subject The subject of the email */
  private $subject = '';
  /** @var String $message The message to send as the body */
  private $message = '';
  /** @var Class The PHPMailer class */
  private $PHPMailer;

  /**
   * Constructor
   * 
   * Constructs the sender and receiever options for the Mail class.
   * Sets up the PHPMailer class to be prepared to be sent
   * @param String $subject Subject of the email
   * @param String $message Body of the email
   * @param String $receiever Email to be sent to
   * @param String $name Name of te receiever
   */
  public function __construct (String $subject = '', String $message = '', String $receiever = '', String $name = '') {
    // Assign email options
    $this->senderEmail = 'info@copytube.com';
    $this->senderName = 'CopyTube';
    $this->receiverEmail = $receiever;
    $this->receieverName = $name; // doesnt affect anything from what ive seen
    $this->subject = $subject;
    $this->message = $message;

    // Prep PHPMailer options
    try {
      $this->PHPMailer = new PHPMailer;
      $this->PHPMailer->isSMTP(); // use SMTP
      $this->PHPMailer->SMTPDebug = 2; // Allow debugging
      $this->PHPMailer->Host = 'smtp.gmail.com'; // gmail host
      $this->PHPMailer->SMTPAuth = true; // allow SMTP auth to pass in email and password
      $this->PHPMailer->Username = 'EdwardSBebbington@gmail.com'; // gmail email
      $this->PHPMailer->Password = ''; // gmail password
      $this->PHPMailer->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
      $this->PHPMailer->Port = 587; // self explantory

      // Setup the final email
      $this->PHPMailer->setFrom($this->senderEmail, $this->senderName);
      $this->PHPMailer->addAddress($this->receiverEmail, $this->receiverName);
      $this->PHPMailer->Subject = $this->subject;
      $this->PHPMailer->Body = $this->message;
    } catch (Exception $e) {
      trigger_error('Error when constructing the PHPMailer class inside the Mail class : ' . $e->getMessage());
    }
  }

  /**
   * Send an Email
   * 
   * Using the email options and PHPMailer setup from the constructor, 
   * send an email to the receiever
   */
  public function send () {
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
