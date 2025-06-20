<?php

namespace App\Http\Requests\Administrative\Users;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
        return [
            'old_password'  => 'required|string|min:6',
            'password'  => ['required', 'regex:/^(?=.*\d)(?=.*[@$!%*?&._-])([A-Za-z\d@$!%*?&._-]|[^ ]){8,}$/', 'confirmed']
        ];
    }
}
