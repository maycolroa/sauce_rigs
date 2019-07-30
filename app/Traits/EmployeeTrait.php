<?php

namespace App\Traits;

use App\Traits\ConfigurableFormTrait;
use Exception;

trait EmployeeTrait
{
    use ConfigurableFormTrait;
    
    public function getRules($params = [])
    {
        $formModel = $this->getFormModel('form_employee');

        if ($formModel == 'default')
        {
            return [
                'identification' => 'required|string|unique:sau_employees,identification,'.$params['id'].',id,company_id,'.$params['company_id'],
                'name' => 'required|string',
                'date_of_birth' => 'nullable|date',
                'sex' => 'required|string|in:Masculino,Femenino',
                'email' => 'nullable|email|unique:sau_employees,email,'.$params['id'].',id,company_id,'.$params['company_id'],
                'income_date' => 'required|date',
                'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
                'employee_headquarter_id' => 'nullable|exists:sau_employees_headquarters,id',
                'employee_process_id' => 'nullable|exists:sau_employees_processes,id',
                'employee_area_id' => 'nullable|exists:sau_employees_areas,id',
                'employee_position_id' => 'required|exists:sau_employees_positions,id',
                'employee_business_id' => 'nullable|exists:sau_employees_businesses,id',
                'employee_eps_id' => 'nullable|exists:sau_employees_eps,id'
            ];
        }
    }
}