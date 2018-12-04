<?php

namespace App\Contact;

use App\Entity\Contact;

class Mailer
{
    private $recipient;
    private $mailer;

    public function __construct(string $recipient, \Swift_Mailer $mailer)
    {
        $this->recipient = $recipient;
        $this->mailer = $mailer;
    }

    public function sendMail(Contact $contact): void
    {
        $message = $this->mailer->createMessage()
            ->setTo($this->recipient)
            ->setFrom($contact->sender)
            ->setSubject($contact->subject)
            ->setBody($contact->message)
        ;

        $this->mailer->send($message);
    }
}
