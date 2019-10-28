<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class TalendRequest extends FormRequest
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
            'name' => 'required|unique:sau_absen_talends,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'file' => 'required|max:20480'
        ];
    }
}
