<?php

namespace App\Http\Requests\Administrative\Headquarters;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class HeadquarterRequest extends FormRequest
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
        $employee_regional_id = $this->input('employee_regional_id');

        return [
            'name' => 'required|string|unique:sau_employees_headquarters,name,'.$id.',id,employee_regional_id,'.$employee_regional_id,
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id'
        ];
    }

    public function messages()
    {
        $keywords = Auth::user()->getKeywords();
        
        return [
            'employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.'
        ];
    }
}
