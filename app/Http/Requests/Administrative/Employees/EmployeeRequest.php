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

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        if ($this->has('locations'))
        {
            $this->merge([
                'locations' => json_decode($this->input('locations'), true)
            ]);
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
            'locations.employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.',
            'locations.employee_headquarter_id.required' => 'El campo '.$keywords['headquarter'].' es obligatorio.',
            'locations.employee_process_id.required' => 'El campo '.$keywords['process'].' es obligatorio.',
            'locations.employee_area_id.required' => 'El campo '.$keywords['area'].' es obligatorio.',
            'locations.employee_position_id.required' => 'El campo '.$keywords['position'].' es obligatorio.'
        ];
    }
}
