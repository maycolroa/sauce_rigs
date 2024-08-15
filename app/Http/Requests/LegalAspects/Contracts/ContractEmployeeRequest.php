<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;
use App\Traits\ContractTrait;
use Illuminate\Support\Facades\Auth;

class ContractEmployeeRequest extends FormRequest
{
    use ContractTrait;

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
        $contract = $this->getContractUser(Auth::user()->id, Session::get('company_id'));

        return [
            'identification' => 'required|string|unique:sau_ct_contract_employees,identification,'.$id.',id,contract_id,'.$contract->id,
            'email' => 'required|email',
            'name' => 'required',
            'position' => 'required',
            'employee_afp_id' => 'required',
            'employee_eps_id' => 'required',
            'sex' => 'required',    
            'phone_residence' => 'nullable',
            'phone_movil' => 'required',
            'direction' => 'required',
            'disability_condition' => 'required',
            'disability_description' => 'required_if:disability_condition,SI',
            'emergency_contact' => 'required',
            'emergency_contact_phone' => 'required',
            'rh' => 'required',
            'salary' => 'required',
            'date_of_birth' => 'required',
            'activities' => 'required|array',
            'activities.*.selected' => 'required',
            'activities.*.documents.*.files.*.name' => 'required',
            'activities.*.documents.*.files.*.file' => 'required',
            'activities.*.documents.*.files.*.expirationDate' => 'required_if:activities.*.documents.*.files.*.required_expiration_date,SI',
            'activities.*.documents.*.apply_motive' => 'required_if:activities.*.documents.*.apply_file,NO|nullable|string|min:30'
        ];
    }

    public function attributes()
    {
        return [
            'activities.*.selected' => 'Actividad',
            'activities.*.documents.*.files.*.name' => 'Nombre',
            'activities.*.documents.*.files.*.file' => 'Archivo',
            'activities.*.documents.*.files.*.expirationDate' => 'Fecha de vencimiento',
            'activities.*.documents.*.files.*.required_expiration_date' => 'Marcador',
            'activities.*.documents.*.apply_motive' => 'Motivo',
            'activities.*.documents.*.apply_file' => '¿Aplica el documento?'
        ];
    }

    public function messages()
    {
        return [
            'activities.*.documents.*.apply_motive.required_if' => 'El campo Motivo es obligatorio cuando ¿Aplica el documento? es NO.'
        ];
    }
}
