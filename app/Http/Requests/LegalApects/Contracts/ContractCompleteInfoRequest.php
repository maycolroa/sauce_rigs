<?php

namespace App\Http\Requests\LegalApects\Contracts;

use Illuminate\Foundation\Http\FormRequest;

class ContractCompleteInfoRequest extends FormRequest
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
            'address' => 'required',
            'phone' => 'required|numeric',
            'legal_representative_name' => 'required|string',
            'SG_SST_name' => 'required|string',
            'environmental_management_name' => 'required|string',
            'economic_activity_of_company' => 'required|string',
            'arl' => 'required|string',
            'number_workers' => 'required',
            'risk_class' => 'required'
        ];
        
        return $rules;
    }
}
