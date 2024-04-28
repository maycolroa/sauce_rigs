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
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
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

        if ($this->has('files_binary') && COUNT($this->files_binary) > 0)
        {
            $data = $this->all();

            foreach ($this->files_binary as $key => $value)
            {
                $allKeys = explode("_", $key);
                $keyDoc = $allKeys[0];
                $keyFile = $allKeys[1];

                $data['documents'][$keyDoc]['files'][$keyFile]['file'] = $value;
            }

            $this->merge($data);
        }

        if ($this->has('arl') && $this->arl && is_array($this->arl))
        {
            foreach ($this->input('arl') as $key => $value)
            {
                $data['arl'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('ips') && $this->ips && is_array($this->ips))
        {
            foreach ($this->input('ips') as $key => $value)
            {
                $data['ips'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('social_security_payment_operator') && $this->social_security_payment_operator && is_array($this->social_security_payment_operator))
        {
            foreach ($this->input('social_security_payment_operator') as $key => $value)
            {
                $data['social_security_payment_operator'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('height_training_centers') && $this->height_training_centers && is_array($this->height_training_centers))
        {
            foreach ($this->input('height_training_centers') as $key => $value)
            {
                $data['height_training_centers'][$key] = json_decode($value, true);
                $this->merge($data);
            }
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
                'high_risk_work' => 'required',
                'high_risk_type_id' => 'array|required_if:high_risk_work,SI'
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
                'arl' => 'required|array',
                'number_workers' => 'required|integer|min:0',
                'risk_class' => 'required',
                'documents.*.files.*.name' => 'required',
                'documents.*.files.*.file' => 'required'
            ];
        }
        
        return $rules;
    }

    public function attributes()
    {
        return [
            'documents.*.files.*.name' => 'Nombre',
            'documents.*.files.*.file' => 'Archivo'
        ];
    }
}
