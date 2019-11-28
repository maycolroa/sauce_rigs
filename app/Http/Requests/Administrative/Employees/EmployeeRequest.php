<?php

namespace App\Http\Requests\Administrative\Employees;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\EmployeeTrait;
use Session;

class EmployeeRequest extends FormRequest
{
    use EmployeeTrait;

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
        $params = [
            'id' => $this->input('id'),
            'company_id' => Session::get('company_id')
        ];

        return $this->getRules($params);
    }

    public function messages()
    {
        $keywords = Auth::user()->getKeywords();
        
        return [
            'employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.',
            'employee_headquarter_id.required' => 'El campo '.$keywords['headquarter'].' es obligatorio.',
            'employee_process_id.required' => 'El campo '.$keywords['process'].' es obligatorio.',
            'employee_area_id.required' => 'El campo '.$keywords['area'].' es obligatorio.',
            'employee_position_id.required' => 'El campo '.$keywords['position'].' es obligatorio.'
        ];
    }
}
