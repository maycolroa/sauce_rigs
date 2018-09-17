<?php

namespace App\Facades\Mail;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use App\Models\MailInformation;
use App\Mail\NotificationGeneralMail;
use App\Models\LogMail;
use App\Models\Module;
use Route;
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

        if (empty($mail->getModule()))
            throw new \Exception(trans('mail.module_empty'));

        if (!Module::find($mail->getModule()))
            throw new \Exception(trans('mail.module_not_exist'));

        try {
            Mail::to($mail->getRecipients())->send(new NotificationGeneralMail($mail));

            $event = explode("\\", Route::currentRouteAction());
            $event = $event[COUNT($event) - 1];

            $log = new LogMail();
            $log->module_id = $mail->getModule();
            $log->event = $event;
            $log->recipients = implode(",", $mail->getRecipients());
            $log->subject = $mail->getSubject();
            $log->message = $mail->getMessage();
            $log->created_at = date("Y-m-d H:i:s");
            $log->save();
        }
        catch (\Exception $e) {
            throw new \Exception(trans('mail.send_error'));
        }
    }
}
