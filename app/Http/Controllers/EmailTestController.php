<?php

namespace App\Http\Controllers;
use App\Models\MailInformation;
use App\Facades\Mail\Facades\NotificationMail;
use App\User;
use App\Models\Module;

class EmailTestController extends Controller
{
    public function index()
    {   
        //$users = User::find(2);
        $users = User::get();
        //$users = ['riera_1992@hotmail.com'];
        //dd($users);
        
        /*$mail = new MailInformation();
        $mail->setRecipients($users);
        $mail->setMessage("Este es un mensaje de prueba");
        $mail->setModule(1);
        //$mail->setFrom(['address'=>'sause@gmail.com', 'name'=>'Roberth Riera']);
        $mail->setFrom('sause@gmail.com');
        //$mail->setSubject('Notification Test');

        return NotificationMail::sendMail($mail);*/

        $buttons = [
            ['text'=>'Descargar', 'url'=>'www.example.com'],
            ['text'=>'Oferta', 'url'=>'www.oferta.com'],
        ];

        $list = [
            'Primer elemento de la lista',
            'Segundo elemanto de la lista',
            'Tercer elemento de la lista'
        ];

        $module = Module::where('name', 'mod 1')->first();
        
        dd(NotificationMail::view('asd')
            ->subject('Notificacion')
            ->message('Mensaje de prueba')
            ->recipients($users)
            ->buttons($buttons)
            ->list($list)
            ->module($module)
            ->send()
        );

    }
}
