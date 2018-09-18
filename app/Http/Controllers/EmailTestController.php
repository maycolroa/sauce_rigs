<?php

namespace App\Http\Controllers;
use App\Models\MailInformation;
use App\Facades\Mail\Facades\NotificationMail;

class EmailTestController extends Controller
{
    public function index()
    {
        $mail = new MailInformation();
        $mail->setRecipients(['riera_1992@hotmail.com']);
        $mail->setMessage("Este es un mensaje de prueba");
        //$mail->setFrom(['address'=>'sause@gmail.com', 'name'=>'Roberth Riera']);
        $mail->setFrom('sause@gmail.com');
        //$mail->setSubject('Notification Test');

        return NotificationMail::sendMail($mail);

    }
}
