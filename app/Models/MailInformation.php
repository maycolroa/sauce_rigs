<?php

namespace App\Models;

class MailInformation
{
    //Recipients to whom the mail will be sent (Array)
    private $recipients;

    //Recipients copied (Array)
    private $cc;

    //Subject of the mail
    private $subject;

    //Mail from where the shipment is being made
    private $from;

    //Mail message
    private $message;

    public function __construct()
    {
        $this->recipients = [];
        $this->cc = [];
        $this->subject = trans('mail.subject');
        $this->from = config('mail.from');
    }

    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function setCc($cc)
    {
        $this->cc = $cc;
    }

    public function getCc()
    {
        return $this->cc;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function setFrom($from)
    {
        if (is_array($from))
        {
            if (isset($from["address"]) && isset($from["name"]))
                $this->from = $from;
        }
        else
            $this->from = $from;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
