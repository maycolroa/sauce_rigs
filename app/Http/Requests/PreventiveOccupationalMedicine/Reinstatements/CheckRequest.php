<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ReinstatementsTrait;
use Session;

class CheckRequest extends FormRequest
{
    use ReinstatementsTrait;

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
        return [
            //'employee_regional_id.required' => 'El campo '.$this->keywordCheck('regional').' es obligatorio.'
            'disease_origin.required' => 'El campo es obligatorio.'
        ];
    }
}
