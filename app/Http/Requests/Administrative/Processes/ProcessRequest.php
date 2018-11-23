<?php

namespace App\Http\Requests\Administrative\Processes;

use Illuminate\Foundation\Http\FormRequest;

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
        $employee_area_id = $this->input('employee_area_id');

        return [
            'name' => 'required|string|unique:sau_employees_processes,name,'.$id.',id,employee_area_id,'.$employee_area_id,
            'employee_area_id' => 'required|exists:sau_employees_areas,id'
        ];
    }
}
