<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;

class TalendUploadRequest extends FormRequest
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
            "file"        => "required|file|max:20480|mimes:zip"
        ];

        return $rules;
    }
}
