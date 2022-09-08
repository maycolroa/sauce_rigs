<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Symfony\Component\Mime\Address;
use App\Models\General\EmailBlackList;
use App\Traits\UtilsTrait;

class CheckEmailLocked
{
    use UtilsTrait;

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
     * @param  MessageSending  $event
     * @return void
     */
    public function handle(MessageSending $event)
    {
        $correos = [];

        if (is_array($event->message->getTo())) {
            foreach ($event->message->getTo() as $correo => $valor) {
                $correos[] = $correo;
            }
        }

        if (is_array($event->message->getCc())) {
            foreach ($event->message->getCc() as $correo => $valor) {
                $correos[] = $correo;
            }
        }

        if (is_array($event->message->getBcc())) {
            foreach ($event->message->getBcc() as $correo => $valor) {
                $correos[] = $correo;
            }
        }

        $except = EmailBlackList::whereIn('email', $correos)->pluck('email')->unique();

        $event->message->setTo($this->filterData($event->message->getTo(), $except));
        $event->message->setCc($this->filterData($event->message->getCc(), $except));
        $event->message->setBcc($this->filterData($event->message->getBcc(), $except));
    }

    public function filterData($data, $except)
    {
        $data = collect($data)->filter(function ($value, $email) use ($except) {
            return $this->validEmail($email) && !$except->contains($email);
        })
        ->toArray();

        return $data;
    }
}