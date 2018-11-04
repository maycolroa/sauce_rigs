<?php

namespace App\Http\Requests\Administrative\Users;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\Configuration;

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
            'role_id'   => 'required'
        ];

        switch($this->method())
        {
            case 'POST':
                $rules['password'] = 'required|string|min:6';
            break;

            case 'PUT':
                $rules['password'] = 'nullable|string|min:6';
            break;

            default:break;
        }

        return $rules;
    }
}
