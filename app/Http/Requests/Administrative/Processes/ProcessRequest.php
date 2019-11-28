<?php

namespace App\Http\Requests\Administrative\Processes;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MacroprocessUnique;
use Illuminate\Support\Facades\Auth;

class ProcessRequest extends FormRequest
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
        if ($this->has('types') && $this->input('types'))
        {
            foreach ($this->input('types') as $key => $value)
            {
                $data['types'][$key] = json_decode($value, true);
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
        $id = $this->input('id');

        return [
            'name' => ['required','string',new MacroprocessUnique($id)],
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
            'employee_headquarter_id' => 'required|array'
        ];
    }

    public function messages()
    {
        $keywords = Auth::user()->getKeywords();
        
        return [
            'employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.',
            'employee_headquarter_id.required' => 'El campo '.$keywords['headquarter'].' es obligatorio.'
        ];
    }
}
