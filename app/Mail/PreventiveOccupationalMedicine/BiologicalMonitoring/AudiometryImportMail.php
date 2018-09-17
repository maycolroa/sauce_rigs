<?php

namespace App\Mail\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AudiometryImportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Importacion de las audiometrias')
                    ->markdown('mail.preventiveoccupationalmedicine.biologicalmonitoring.audiometryimport')
                    ->with(['message' => $this->message]);
    }
}
