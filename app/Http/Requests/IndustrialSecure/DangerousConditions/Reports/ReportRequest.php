<?php

namespace App\Http\Requests\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ConfigurableFormTrait;
use Session;

class ReportRequest extends FormRequest
{
    use ConfigurableFormTrait;

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
        return [
            'condition_id' => 'required|exists:sau_ph_conditions,id',
            'rate' => 'required',
            'observation' => 'required',
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
            'employee_headquarter_id' => 'required|exists:sau_employees_headquarters,id',
            'employee_process_id' => 'required|exists:sau_employees_processes,id',
            'employee_area_id' => 'required|exists:sau_employees_areas,id',
        ];

        return $this->getRules($params);
    }

    public function messages()
    {
        return [
            'employee_regional_id.required' => 'El campo '.$this->keywordCheck('regional').' es obligatorio.'
        ];
    }
}
