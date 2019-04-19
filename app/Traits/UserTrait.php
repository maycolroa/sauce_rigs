<?php

namespace App\Traits;

use App\SecApplication;
use App\Models\Administrative\Users\User;
use App\Http\Requests\Administrative\Users\UserRequest;
use App\UserLog;
use App\Models\Administrative\Users\GeneratePasswordUser;
use Illuminate\Support\Facades\Session;
use App\Facades\Mail\Facades\NotificationMail;

trait UserTrait
{
    public function createUser($request)
    {
        $user = new User($request->all());
        $generatePasswordUser = new GeneratePasswordUser();

        if(!$user->save()){
            return null;
        }

        if (!$request->password) {
            $generatePasswordUser->user_id = $user->id;
            $generatePasswordUser->token = bcrypt($user->email.$user->document);
            $generatePasswordUser->save();
            
            if (!$generatePasswordUser->save()) {
                return null;
            }

            NotificationMail::
            subject('Creación de usuario en sauce')
            ->message('Te damos la bienvenida a la plataforma, se ha generado un nuevo usuario para este correo, para establecer tu nueva contraseña, por favor dar click al siguiente enlace.')
            ->recipients($user)
            ->buttons([['text'=>'Establecer contraseña', 'url'=>url("/password/generate/".base64_encode($generatePasswordUser->token))]])
            ->module('users')
            ->subcopy('Este link sólo se puede utilizar una vez')
            ->send();
        }
        
        return $user;
      
    }
}