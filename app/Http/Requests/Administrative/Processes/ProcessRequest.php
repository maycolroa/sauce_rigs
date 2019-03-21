<?php

namespace App\Http\Requests\Administrative\Processes;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\MacroprocessUnique;

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
}
