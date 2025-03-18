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
            foreach ($this->input('questions') as $key => $value)
            {
                $data['questions'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('files'))
        {
            foreach ($this->input('files') as $key => $value)
            {
                $data['files'][$key] = json_decode($value, true);
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
                $data['files'][$key]['file'] = $value;
            }

            $this->merge($data);
        }

        if ($this->has('files_binary_intern') && COUNT($this->files_binary_intern) > 0)
        {
            $data = $this->all();

            foreach ($this->files_binary_intern as $key => $value)
            {
                $allKeys = explode("_", $key);
                $keyQues = $allKeys[0];
                $keyImg = $allKeys[1];
                $keyFile = $allKeys[2];

                $data['questions'][$keyQues]['images'][$keyImg]['files'][$keyFile]['file'] = $value;
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
        \Log::info($this);
        $id = $this->input('id');

        return [
            'name' => 'required|unique:sau_ct_trainings,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'number_questions_show' => 'required|min:1',
            'min_calification'  => "required|integer|min:1",//|between:1,".$this->input('max_calification'),
           // 'max_calification'  => 'required|min:1',
            'number_attemps'  => 'required|min:1',
            'activity_id' => 'required',
            'questions' => 'required|array',
            'questions.*.description' => 'required',
            'questions.*.type_question_id' => 'required',
            //'questions.*.value_question' => 'required',
            'questions.*.options' => 'required_if:questions.*.type_question_id,1,3',
            'questions.*.answers' => 'required',
            /*'questions.*.images' => 'required_if:questions.*.type_question_id,7,8',
            'questions.*.images.*.file' => 'required_if:questions.*.type_question_id,7,8',
            'questions.*.images.*.text' => 'required_if:questions.*.type_question_id,8'*/
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
            'questions.*.options.required_if' => 'El campo Opciones de respuesta es obligatorio',
            /*'questions.*.images.required_if' => 'El campo Imagen es obligatorio',
            'questions.*.images.*.file.required_if' => 'El campo Imagen es obligatorio',
            'questions.*.images.*.text.required_if' => 'El campo Texto es obligatorio',*/
        ];
    }
}
