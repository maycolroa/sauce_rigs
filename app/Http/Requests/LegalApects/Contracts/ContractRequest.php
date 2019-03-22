<?php

namespace App\Http\Requests\LegalApects\Contracts;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
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

        if ($this->role == "Contratista") {
            $rules = [
                'name'      => 'required|string',
                'email'     => 'required|email|unique:sau_users,email,' . $id . ',id',
                'document'  => 'required|numeric',
                'role'   => 'required',
                'password'  => 'nullable|string|min:6',
                'name_business' => 'required|string',
                'nit' => 'required|numeric',
                'classification' => 'required',
                'social_reason' => 'required|string'
            ];
            return $rules;
        }

        $rules = [
            'name'      => 'required|string',
            'email'     => 'required|email|unique:sau_users,email,' . $id . ',id',
            'document'  => 'required|numeric',
            'role'   => 'required',
            'password'  => 'nullable|string|min:6',
            'name_business' => 'required|string',
            'nit' => 'required|numeric',
            'social_reason' => 'required|string'
        ];
        
        return $rules;
    }
}
