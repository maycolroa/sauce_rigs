<?php

namespace App\Http\Requests\LegalAspects\Evaluations;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class EvaluationRequest extends FormRequest
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
        /*$this->merge([
            'objectives' => json_decode($this->input('objectives'), true)
        ]);*/

        if ($this->has('objectives'))
        {
            foreach ($this->input('objectives') as $key => $value)
            {
                $data['objectives'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

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
            'name' => 'required|string|unique:sau_ct_evaluations,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'type' => 'required',
            'evaluators_id' => 'required|array',
            'information_contract_lessee_id' => 'required|exists:sau_ct_information_contract_lessee,id',
            'interviewees' => 'nullable|array',
            'interviewees.*.name' => 'required',
            'objectives' => 'required|array',
            'objectives.*.description' => 'required',
            'objectives.*.subobjectives' => 'required|array',
            'objectives.*.subobjectives.*.description' => 'required',
            'objectives.*.subobjectives.*.items' => 'required|array',
            'objectives.*.subobjectives.*.items.*.description' => 'required',
        ];
    }
}
