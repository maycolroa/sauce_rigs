<?php

namespace App\Http\Requests\IndustrialSecure\RiskMatrix;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class SubProcessRequest extends FormRequest
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

        return [
            'name' => 'required|string|unique:sau_rm_sub_processes,name,'.$id.',id,company_id,'.Session::get('company_id'),
        ];
    }
}
