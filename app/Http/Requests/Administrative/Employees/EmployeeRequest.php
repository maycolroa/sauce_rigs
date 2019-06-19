<?php

namespace App\Http\Requests\Administrative\Employees;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class EmployeeRequest extends FormRequest
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
            'identification' => 'required|string|unique:sau_employees,identification,'.$id.',id,company_id,'.Session::get('company_id'),
            'name' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'sex' => 'required|string|in:M,F',
            'email' => 'required|email|unique:sau_employees,email,'.$id.',id,company_id,'.Session::get('company_id'),
            'income_date' => 'required|date',
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
            'employee_headquarter_id' => 'required|exists:sau_employees_headquarters,id',
            'employee_area_id' => 'required|exists:sau_employees_areas,id',
            'employee_process_id' => 'required|exists:sau_employees_processes,id',
            'employee_position_id' => 'required|exists:sau_employees_positions,id',
            'employee_business_id' => 'nullable|exists:sau_employees_businesses,id',
            'employee_eps_id' => 'required|exists:sau_employees_eps,id',
        ];
    }
}
