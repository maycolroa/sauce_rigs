<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\Rules\Api\CheckCompany;
use App\Rules\Api\CheckLicense;
use App\Rules\Api\CheckLocationConfiguration;
use Constant;

class InspectionQualificationsRequest extends FormRequest
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

    /*public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        $this->merge([
            'inspections' => json_decode($this->input('inspections'), true)
        ]);

        return $this->all();
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //$id = $this->input('id');

        return [
            'company_id' => ['required', 'numeric', new CheckCompany(Auth::guard('api')->user()->id), new CheckLicense()],
            'inspection_id' => 'required|numeric',
            'items' => 'required|array',
            'employee_regional_id' => [new CheckLocationConfiguration($this->input('company_id'))],
            'employee_headquarter_id' => [new CheckLocationConfiguration($this->input('company_id'))],
            'employee_process_id' => [new CheckLocationConfiguration($this->input('company_id'))],
            'employee_area_id' => [new CheckLocationConfiguration($this->input('company_id'))]
        ];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    /*public function messages()
    {
        return [
            'evaluation.objectives.*.subobjectives.*.items.*.ratings.*.value.required_if' => 'Requerido'
        ];
    }*/
}
