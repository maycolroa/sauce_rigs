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
        if ($this->has('objectives'))
        {
            foreach ($this->input('objectives') as $key => $value)
            {
                $data['objectives'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('types_rating'))
        {
            foreach ($this->input('types_rating') as $key => $value)
            {
                $data['types_rating'][$key] = json_decode($value, true);
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
            'types_rating' => 'required|array',
            'objectives' => 'required|array',
            'objectives.*.description' => 'required',
            'objectives.*.subobjectives' => 'required|array',
            'objectives.*.subobjectives.*.description' => 'required',
            'objectives.*.subobjectives.*.items' => 'required|array',
            'objectives.*.subobjectives.*.items.*.description' => 'required',
        ];
    }
}
