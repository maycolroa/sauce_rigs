<?php

namespace App\Traits;

use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\GeneratePasswordUser;
use App\Facades\Mail\Facades\NotificationMail;
use App\Traits\ResponseTrait;
use Session;
use Hash;

trait UserTrait
{
    use ResponseTrait;

    public function createUser($request)
    {
        $document_exist = User::where('document', $request->document)->active()->first();

        if (!$document_exist)
        {            
            $user = new User($request->all());
            $user->api_token = Hash::make($user->document . str_random(10));
            
            $generatePasswordUser = new GeneratePasswordUser();

            if(!$user->save()){
                return null;
            }

            $user->companies()->sync(Session::get('company_id'));

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
                    ->company(Session::get('company_id'))
                    ->send();
            }

            return $user;
        }
        else
        {
            $user = "Documento repetido";

            return $user;
        }      
    }

    public function resendMail($user)
    {
        GeneratePasswordUser::
              where('user_id', $user->id)
            ->where('state', 'without use')
            ->update(['state' => 'used']);

        $generatePasswordUser = new GeneratePasswordUser();
        $generatePasswordUser->user_id = $user->id;
        $generatePasswordUser->token = bcrypt($user->email.$user->document);
        $generatePasswordUser->save();
        
        if (!$generatePasswordUser->save()) {
            return false;
        }

        NotificationMail::
            subject('Creación de usuario en sauce')
            ->message('Te damos la bienvenida a la plataforma, se ha generado un nuevo usuario para este correo, para establecer tu nueva contraseña, por favor dar click al siguiente enlace.')
            ->recipients($user)
            ->buttons([['text'=>'Establecer contraseña', 'url'=>url("/password/generate/".base64_encode($generatePasswordUser->token))]])
            ->module('contracts')
            ->subcopy('Este link sólo se puede utilizar una vez')
            ->company(Session::get('company_id'))
            ->send();

        return true;
    }
}