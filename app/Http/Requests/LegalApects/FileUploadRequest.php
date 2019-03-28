<?php

namespace App\Http\Requests\LegalAspects;

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
        return [
            "name" => "required",
            "file" => "required|max:500000",
            "expirationDate" => "nullable|date|after_or_equal:today"
        ];
    }
}
