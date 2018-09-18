<?php

namespace App\Facades\Mail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use App\Models\MailInformation;
use App\Mail\NotificationGeneralMail;
use Exception;

class NotificationMail
{
    public function __construct()
    {

    }

    public static function sendMail(MailInformation $mail)
    {
        if (empty($mail->getRecipients()))
            throw new \Exception(trans('mail.recipient_empty'));

        if (empty($mail->getMessage()))
            throw new \Exception(trans('mail.message_empty'));

        try {
            Mail::to($mail->getRecipients())->send(new NotificationGeneralMail($mail));
        }
        catch (\Exception $e) {
            throw new \Exception(trans('mail.send_error'));
        }
    }
}
