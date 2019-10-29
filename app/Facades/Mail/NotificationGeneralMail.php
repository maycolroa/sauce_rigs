<?php

namespace App\Facades\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationGeneralMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
      /*if($this->mail->view == 'notification'){
        return $this->subject($this->mail->subject)
                    ->markdown('mail.'.$this->mail->view)
                    ->with(['mail' => $this->mail]);
      }
      else{
        return $this->subject($this->mail->subject)
                    ->markdown('mail.'.$this->mail->view)
                    ->with(['mail' => $this->mail]);
      }*/

      $mail = $this->subject($this->mail->subject)
                    ->markdown('mail.'.$this->mail->view)
                    ->with(['mail' => $this->mail]);

      if (COUNT($this->mail->attach) > 0)
      {
        foreach ($this->mail->attach as $key => $path)
        {
          $mail->attach($path);
        }
      }

      return $mail;
    }
}
