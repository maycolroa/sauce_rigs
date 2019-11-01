<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class EvaluationContractRequest extends FormRequest
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
        $this->merge([
            'evaluation' => json_decode($this->input('evaluation'), true)
        ]);

        if ($this->has('interviewees'))
        {
            foreach ($this->input('interviewees') as $key => $value)
            {
                $data['interviewees'][$key] = json_decode($value, true);
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
                $keyObj = $allKeys[0];
                $keySubObj = $allKeys[1];
                $keyItem = $allKeys[2];
                $keyFile = $allKeys[3];

                $data['evaluation']['objectives'][$keyObj]['subobjectives'][$keySubObj]['items'][$keyItem]['files'][$keyFile]['file'] = $value;
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
            'evaluators_id' => 'required|array',
            'contract_id' => 'required|exists:sau_ct_information_contract_lessee,id',
            'interviewees' => 'nullable|array',
            'interviewees.*.name' => 'required',
            //'evaluation.objectives.*.subobjectives.*.items.*.ratings.*.value' => 'required_if:evaluation.objectives.*.subobjectives.*.items.*.ratings.*.apply,SI',
            'evaluation.objectives.*.subobjectives.*.items.*.observations.*.description' => 'required',
            'evaluation.objectives.*.subobjectives.*.items.*.files.*.file' => 'max:20480'
        ];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function messages()
    {
        return [
            'evaluation.objectives.*.subobjectives.*.items.*.ratings.*.value.required_if' => 'Requerido'
        ];
    }
}
