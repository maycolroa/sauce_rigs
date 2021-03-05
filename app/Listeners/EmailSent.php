<?php

namespace App\Listeners;

use jdavidbakr\MailTracker\Events\EmailSentEvent;
use App\Models\System\LogMails\LogMail;

class EmailSent
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EmailSentEvent  $event
     * @return void
     */
    public function handle(EmailSentEvent $event)
    {
        $tracker = $event->sent_email;
        $model_id = $event->sent_email->getHeader('X-Model-ID');
        $message_id = $event->sent_email->getHeader('Message-ID');

        if ($model_id && $message_id)
        {
            $message_id = str_replace("<", "", $message_id);
            $message_id = str_replace(">", "", $message_id);

            $model = LogMail::find($model_id);
            $model->update(['message_id' => $message_id]);
            // Perform your tracking/linking tasks on $model knowing the SentEmail object
        }
    }
}