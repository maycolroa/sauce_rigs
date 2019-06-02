<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Administrative\Users\GeneratePasswordUser;
use App\Models\Administrative\Users\User;

class GeneratePasswordController extends Controller
{ 
    /**
     * Undocumented function
     *
     * @param [type] $tokenEncode
     * @return void
     */
    public function generatePassword($tokenEncode)
    {
        $token = base64_decode($tokenEncode);
        $user = GeneratePasswordUser::where('token', $token)->first();
        // dd($token);
        if (!$user) {
            return $this->respondHttp500();            
        }
        if ($user->state != 'without use') {
            return view('auth.passwords.generate', ['state' => $user->state, 'user_id' => $user->user_id]);
        } else {
            return view('auth.passwords.generate', ['state' => $user->state, 'user_id' => $user->user_id]);
        }
        // dd($user);
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param [type] $id
     * @return void
     */
    public function updatePassword(Request $request, $id)
    {

        $validationPassword = $request->validate([
            'password' => 'required|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = $request->password;
        $user->save();

        $userPass = GeneratePasswordUser::
                  where('user_id', $id)
                ->where('state', 'without use')
                ->first();
                
        $userPass->state = 'used';
        $userPass->save();

        return $this->respondHttp200([
            'message' => 'Se ha generado la contraseña correctamente'
        ]);
        
    }
}
