<?php

namespace App\Http\Requests\LegalAspects\LegalMatrix;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class SystemApplyRequest extends FormRequest
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
        $rules = [];

        if ($this->input('custom'))
            $rules['name'] = 'required|unique:sau_lm_system_apply,name,'.$id.',id,company_id,'.Session::get('company_id');
        else
            $rules['name'] = 'required|unique:sau_lm_system_apply,name,'.$id.',id';

        return $rules;
    }
}
