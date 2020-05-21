<?php

namespace App\Http\Controllers\Api;

use Hash;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use Auth;

class AuthController extends ApiController
{
    public function login(LoginRequest $request)
    {
        $user = User::select(
            'sau_users.*'
        )
        ->where('document', $request->document)
        ->first();

        if (!$user) 
        {
            return $this->responderError('Usuario no existe');
        }

        if ($user && Hash::check($request->input('password'), $user->password)) 
        {
            return $this->responderOk(['user' => $user]);
        }

        return $this->responderError('Credenciales incorrectas');
    }

    private function responderError($data)
    {
        return $this->responder('error', $data);
    }

    private function responderOk($data)
    {
        return $this->responder('ok', $data);
    }

    private function responder($type, $data)
    {
        $response = [
            'response' => $type,
            'data' => $data
        ];
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}