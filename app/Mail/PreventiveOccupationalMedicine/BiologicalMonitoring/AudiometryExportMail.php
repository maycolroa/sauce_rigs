<?php

namespace App\Mail\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AudiometryExportMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Exportacion de las audiometrias')
                    ->markdown('mail.preventiveoccupationalmedicine.biologicalmonitoring.audiometryexport')
                    ->with(['url' => $this->url]);
    }
}
