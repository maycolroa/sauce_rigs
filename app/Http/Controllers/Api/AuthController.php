<?php

namespace App\Http\Controllers\Api;

use Hash;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Facades\General\PermissionService;
use Auth;

class AuthController extends ApiController
{
    public function login(LoginRequest $request)
    {
        \Log::info($request);
        if($request->has('email'))
        {
            $user = User::select(
                'sau_users.*'
            )
            ->where('email', $request->email)
            ->active()
            ->first(); 
        }
        else
        {
            $user = User::select(
                'sau_users.*'
            )
            ->where('document', $request->document)
            ->active()
            ->first();
        }

        if (!$user) 
        {
            return $this->responderError('Usuario no existe');
        }

        if ($user && Hash::check($request->input('password'), $user->password)) 
        {
            if ($user->active == 'SI')
            {
                $companies = PermissionService::getCompaniesActive($user);
                $module = PermissionService::getModuleByName('dangerousConditions');
                $module_2 = PermissionService::getModuleByName('epp');

                $companies = $companies->filter(function ($item, $keyCompany) use ($module, $module_2) {
                    return PermissionService::existsLicenseByModule($item["id"], [$module->id, $module_2->id]);
                })
                ->map(function ($item, $keyCompany) use ($user) {
                    return [
                        'id'      => $item['id'],
                        'name'    => $item['name'],
                        'hasRole' => PermissionService::getHasRole($user, $item["id"]),
                        'can'     => PermissionService::getCan($user, $item["id"])
                    ];
                });

                if ($companies->count() > 0)
                {
                    if (!$request->has('email'))
                        $user->companies = $companies->values();

                    return $this->responderOk(['user' => $user]);
                }
                else 
                {
                    return $this->responderError('Estimado Usuario no posee una compañia activa para poder ingresar al sistema');
                }    

            }
            else
                return $this->responderError('Usuario inhabilitado');
        }
        else
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