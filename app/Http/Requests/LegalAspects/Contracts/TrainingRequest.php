<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class TrainingRequest extends FormRequest
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

    
    /*public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
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

        return $this->all();
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->input('id');

        return [
            'name' => 'required|unique:sau_ct_trainings,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'number_questions_show' => 'required|min:1',
            'min_calification'  => "required|integer|min:1|between:1,".$this->input('max_calification'),
            'max_calification'  => 'required|min:1',
            'number_attemps'  => 'required|min:1',
            'activity_id' => 'required'
        ];
    }
}
