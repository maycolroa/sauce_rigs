<?php

namespace App\Http\Requests\IndustrialSecure\RoadSafety\Training;

use Illuminate\Foundation\Http\FormRequest;

class TrainingEmployeeRequest extends FormRequest
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
        if ($this->has('questions'))
        {
            foreach ($this->input('questions') as $keyAct => $value)
            {
                $data['questions'][$keyAct] = json_decode($value, true);
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
        return [];
    }
}
