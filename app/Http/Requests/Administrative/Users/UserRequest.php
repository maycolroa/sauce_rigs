<?php

namespace App\Http\Requests\Administrative\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\General\Team;
use Session;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->input('id');

        $rules = [
            'name'      => 'required|string',
            'email'     => 'required|email|unique:sau_users,email,' . $id . ',id',
            'document'  => 'required|numeric',
            'password'  => 'nullable|string|min:6'
        ];

        $team = Team::where('name', Session::get('company_id'))->first()->id;

        if (!Auth::user()->hasRole('Arrendatario', $team) && !Auth::user()->hasRole('Contratista', $team) && $this->input('edit_role') == 'true')
            $rules['role_id'] = 'required';
        
        return $rules;
    }
}
