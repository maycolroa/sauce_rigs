<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class ContractEmployeeRequest extends FormRequest
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
        if ($this->has('activities'))
        {
            foreach ($this->input('activities') as $keyAct => $value)
            {
                $data['activities'][$keyAct] = json_decode($value, true);
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
                $keyAct = $allKeys[0];
                $keyDoc = $allKeys[1];
                $keyFile = $allKeys[2];

                $data['activities'][$keyAct]['documents'][$keyDoc]['files'][$keyFile]['file'] = $value;
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
            'identification' => 'required',
            'email' => 'required|email',
            'name' => 'required',
            'position' => 'required',
            'activities.*.selected' => 'required',
            'activities.*.documents.*.files.*.name' => 'required',
            'activities.*.documents.*.files.*.file' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'activities.*.selected' => 'Actividad',
            'activities.*.documents.*.files.*.name' => 'Nombre',
            'activities.*.documents.*.files.*.file' => 'Archivo'
        ];
    }
}
