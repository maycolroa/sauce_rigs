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
                $correos[] = $valor->getAddress();
            }
        }

        if (is_array($event->message->getCc())) {
            foreach ($event->message->getCc() as $correo => $valor) {
                $correos[] = $valor->getAddress();
            }
        }

        if (is_array($event->message->getBcc())) {
            foreach ($event->message->getBcc() as $correo => $valor) {
                $correos[] = $valor->getAddress();
            }
        }

        $except = EmailBlackList::whereIn('email', $correos)->pluck('email')->unique();

        foreach ($this->filterData($event->message->getTo(), $except) as $key => $value)
        {
            if ($key == 0)
                $event->message->to($value);
            else
                $event->message->addTo($value);
        }

        foreach ($this->filterData($event->message->getCc(), $except) as $key => $value)
        {
            if ($key == 0)
                $event->message->cc($value);
            else
                $event->message->addCc($value);
        }

        foreach ($this->filterData($event->message->getBcc(), $except) as $key => $value)
        {
            if ($key == 0)
                $event->message->bcc($value);
            else
                $event->message->addBcc($value);
        }
    }

    public function filterData($data, $except)
    {
        $data = collect($data)->filter(function ($value, $email) use ($except) {
            return $this->validEmail($value->getAddress()) && !$except->contains($value->getAddress());
        })
        ->map(function ($value, $key) {
            return $value->getAddress();
        })
        ->toArray();

        return $data;
    }
}