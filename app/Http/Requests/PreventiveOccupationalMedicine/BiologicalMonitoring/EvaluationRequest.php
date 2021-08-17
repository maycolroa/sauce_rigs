<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\BiologicalMonitoring;

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
        if ($this->has('stages'))
        {
            foreach ($this->input('stages') as $key => $value)
            {
                $data['stages'][$key] = json_decode($value, true);
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
            'stages' => 'required|array',
            'stages.*.description' => 'required',
            'stages.*.criterion' => 'required|array',
            'stages.*.criterion.*.description' => 'required',
            'stages.*.criterion.*.items' => 'required|array',
            'stages.*.criterion.*.items.*.description' => 'required',
        ];
    }
}
