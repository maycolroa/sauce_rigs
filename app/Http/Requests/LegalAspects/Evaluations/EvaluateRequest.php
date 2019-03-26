<?php

namespace App\Http\Requests\LegalAspects\Evaluations;

use Illuminate\Foundation\Http\FormRequest;

class EvaluateRequest extends FormRequest
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
        if ($this->has('objectives'))
        {
            foreach ($this->input('objectives') as $key => $value)
            {
                $data['objectives'][$key] = json_decode($value, true);
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
        return [
            'objectives.*.subobjectives.*.items.*.ratings.*.value' => 'required_if:objectives.*.subobjectives.*.items.*.ratings.*.apply,SI',
            'objectives.*.subobjectives.*.items.*.observations.*.description' => 'required'
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
            'objectives.*.subobjectives.*.items.*.ratings.*.value.required_if' => 'Requerido'
        ];
    }
}
