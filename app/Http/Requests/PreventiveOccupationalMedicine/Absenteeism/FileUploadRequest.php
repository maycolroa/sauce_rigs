<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
        $rules = [
            "name" => "required",
            "file" => "required|max:20480|mimes:xls,xlsx,zip"
        ];

        return $rules;
    }
}
