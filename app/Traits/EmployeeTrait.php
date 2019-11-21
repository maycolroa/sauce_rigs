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

        $rules = [
            'identification' => 'required|string|unique:sau_employees,identification,'.$params['id'].',id,company_id,'.$params['company_id'],
            'name' => 'required|string',
            'date_of_birth' => 'nullable|date',
            'sex' => 'required|string|in:Masculino,Femenino,Sin Sexo',
            'email' => 'nullable|email|unique:sau_employees,email,'.$params['id'].',id,company_id,'.$params['company_id'],
            'income_date' => 'required|date',
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
            'employee_headquarter_id' => 'required|exists:sau_employees_headquarters,id',
            'employee_process_id' => 'required|exists:sau_employees_processes,id',
            'employee_area_id' => 'nullable|exists:sau_employees_areas,id',
            'employee_position_id' => 'required|exists:sau_employees_positions,id',
            'employee_business_id' => 'nullable|exists:sau_employees_businesses,id',
            'employee_eps_id' => 'nullable|exists:sau_employees_eps,id',
            'employee_afp_id' => 'nullable|exists:sau_employees_afp,id',
            'mobile' => 'nullable|numeric',
            'extension' => 'nullable|numeric'
        ];

        if ($formModel == 'misionEmpresarial')
        {
            $rules = array_merge($rules, [
                'employee_arl_id' => 'nullable|exists:sau_employees_arl,id',
                'contract_numbers' => 'required|integer|min:0',
                'contract_type' => 'required'
            ]);
        }

        return $rules;
    }
}