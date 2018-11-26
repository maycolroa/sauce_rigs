<?php

namespace App\Http\Requests\Administrative\Areas;

use Illuminate\Foundation\Http\FormRequest;

class AreaRequest extends FormRequest
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
        $employee_headquarter_id = $this->input('employee_headquarter_id');

        return [
            'name' => 'required|string|unique:sau_employees_areas,name,'.$id.',id,employee_headquarter_id,'.$employee_headquarter_id,
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
            'employee_headquarter_id' => 'required|exists:sau_employees_headquarters,id',
        ];
    }
}
