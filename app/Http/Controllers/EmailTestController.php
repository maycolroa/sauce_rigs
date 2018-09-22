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
            ['text'=>'Descargar', 'url'=>'www.example.com', 'color'=>'green'],
            ['text'=>'Oferta', 'url'=>'www.oferta.com'],
        ];

        $list = [
            'Primer elemento de la lista',
            'Segundo elemanto de la lista',
            'Tercer elemento de la lista'
        ];

        $table = $users;
        /*$table = [
            ['name'=>'Luis',   'mail'=>'luis@gmail.com',     'age'=>32],
            ['name'=>'Miguel', 'mail'=>'miguel@hotmail.com', 'age'=>24],
            ['name'=>'Arturo', 'mail'=>'arturo@outlook.com', 'age'=>30]
        ];*/

        //$module = Module::where('name', 'mod')->first();

        dd(NotificationMail::
                //Recibe string con el nombre de la vista que sera utilizada en el correo. Default: notificacion
              view('notificacion')
                //Recibe string con el asunto del correo. Default: Notificacion
            ->subject('Consulta')
                //Recibe un string con un mensaje que sera incluido en el correo
            ->message('Mensaje de prueba')
                //Recibe un objeto o una colleccion de tipo User para utilizarlos como destinatarios
            ->recipients($users)
                /** Recibe un arreglo de botones
                 *  $buttons = [
                 *      ['text'=>'Descargar', 'url'=>'www.example.com', 'color'=>'green'],
                 *      ['text'=>'Oferta', 'url'=>'www.oferta.com'],
                 *  ];
                 *  Donde el parametro 'color' es opcional. 
                 *  Nota: En la vista por defecto 'notificacion' solo se muestra el primer boton del arreglo
                 * */
            ->buttons($buttons)
                /** Recibe un arreglo simple con los elementos de la lista
                *  $list = ['item_1', 'item_2', 'item_3']
                */
            ->list($list, true)
                /**
                 * Recibe una coleccion o un arreglo con los datos que formaran la tabla
                 * $data = [
                 *      ['name'=>'Luis',   'mail'=>'luis@gmail.com',     'age'=>32],
                 *      ['name'=>'Miguel', 'mail'=>'miguel@hotmail.com', 'age'=>24]
                 *   ];
                 */
            ->table($data)
                //Recibe un objeto Module o un string con el nombre del modulo
            ->module('mod')
                //Recibe un string con el mensaje que saldra al final de la pagina
            ->subcopy('Link valido por 24 horas')
                /** Recibe un array con los datos que seran enviados a la vista
                 * Al llegar a la vista podran ser accedidos por with->param_#
                 * Donde # es el orden en que fue enviado el parametro
                 * */
            ->with(['a'=>'ad', 'qkdmq', 'c'=>'wwd'])
                // Envia el correo
            ->send()
        );

    }
}
