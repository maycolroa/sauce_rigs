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

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        if ($this->has('documents'))
        {
            foreach ($this->input('documents') as $key => $value)
            {
                $data['documents'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
            ]);
        }

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->input('id');

        if (!$this->has('isInformation'))
        {
            $rules = [
                'type'      => 'required',
                'business_name' => 'required|string',
                'nit' => 'required|numeric',//unique:sau_ct_information_contract_lessee,nit,' . $id . ',id,company_id,'.Session::get('company_id'),
                'social_reason' => 'required|string',
                'high_risk_work' => 'required'
            ];

            if (!$id)
            {
                $rules['name'] = 'required|string';
                $rules['email'] = 'required|email';//|unique:sau_users,email';
                $rules['document'] = 'required';
            }

            if ($this->type == "Contratista")
                $rules['classification'] = 'required';
        }
        else
        {
            $rules = [
                'address' => 'required',
                'phone' => 'required|numeric',
                'legal_representative_name' => 'required|string',
                'SG_SST_name' => 'required|string',
                'environmental_management_name' => 'required|string',
                'economic_activity_of_company' => 'required|string',
                'arl' => 'required|string',
                'number_workers' => 'required|integer|min:0',
                'risk_class' => 'required'
            ];
        }
        
        return $rules;
    }
}
