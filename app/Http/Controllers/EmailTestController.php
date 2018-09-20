<?php

namespace App\Http\Controllers;

use App\Facades\Mail\Facades\NotificationMail;
use App\User;
use App\Models\Module;

class EmailTestController extends Controller
{
    public function index()
    {   
        //$users = User::find(2);
        $users = User::get();

        $buttons = [
            ['text'=>'Descargar', 'url'=>'www.example.com'/*, 'color'=>'green'*/],
            ['text'=>'Oferta', 'url'=>'www.oferta.com'],
        ];

        $list = [
            'Primer elemento de la lista',
            'Segundo elemanto de la lista',
            'Tercer elemento de la lista'
        ];

        $columns = ['Name','Email','Age'];

        $data = [
            ['Luis', 'luis@gmail.com', 32],
            ['Miguel', 'miguel@hotmail.com', 24],
            ['Arturo', 'arturo@outlook.com', 30]
        ];

        $module = Module::where('name', 'mod 1')->first();
        
        dd(NotificationMail::/*view('asd')
            ->*/subject('Notificacion')
            ->message('Mensaje de prueba')
            ->recipients($users)
            ->buttons($buttons)
            ->list($list)
            ->table($columns, $data)
            ->module($module)
            ->send()
        );

    }
}
