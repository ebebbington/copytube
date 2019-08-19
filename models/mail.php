<?php

class Mail {

  private $sender;
  private $receiver;
  private $subject;
  private $message;
  private $header;

  public function __construct ($receiver, $subject, $message) {
    $this->sender = '';
    $this->receiver = $receiver;
    $this->subject = $subject;
    $this->message = $message;
    $this->header = 'From: noreply@copytube.com';

    $this->sendMail();
  }

  private function sendMail () {
    mail(
      $this->receiver,
      $this->subject,
      $this->message,
      $this->header
    );
  }
}
