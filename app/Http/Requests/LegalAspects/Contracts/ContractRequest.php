<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;

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

        $rules = [
            'type'      => 'required',
            'business_name' => 'required|string',
            'nit' => 'required|numeric|unique:sau_ct_information_contract_lessee,nit,' . $id . ',id,company_id,'.Session::get('company_id'),
            'social_reason' => 'required|string'
        ];

        if (!$id)
        {
            $rules['name'] = 'required|string';
            $rules['email'] = 'required|email|unique:sau_users,email';
            $rules['document'] = 'required';
        }

        if ($this->type == "Contratista")
            $rules['classification'] = 'required';
        
        return $rules;
    }
}
