<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class InformContractRequest extends FormRequest
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
            $this->sanitize(), $this->container->call([$this, 'rules'])//, $this->messages()
        );
    }

    public function sanitize()
    {
        $this->merge([
            'inform' => json_decode($this->input('inform'), true)
        ]);

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
                $keyObj = $allKeys[0];
                $keyItem = $allKeys[1];
                $keyFile = $allKeys[2];

                $data['inform']['themes'][$keyObj]['items'][$keyItem]['files'][$keyFile]['file'] = $value;
            }

            $this->merge($data);
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

        return [
            'contract_id' => 'required|exists:sau_ct_information_contract_lessee,id',
            'inform.themes.*.items.*.observations.*.description' => 'required',
            'inform.themes.*.items.*.files.*.file' => 'max:20480'
        ];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function messages()
    {
    }
}
