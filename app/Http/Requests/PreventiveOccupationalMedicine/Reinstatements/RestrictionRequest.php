<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class RestrictionRequest extends FormRequest
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
            'state' => 'required|string|unique:sau_reinc_restrictions,state,'.$id.',id,company_id,'.Session::get('company_id'),
        ];
    }
}
