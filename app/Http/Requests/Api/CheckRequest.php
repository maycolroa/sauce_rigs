<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class CheckRequest extends FormRequest
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
            'codigoEmpleado' => 'nullable',
            'documentoEmpleado' => 'nullable',
            'fechaInicio' => 'nullable',
            'fechaFin' => 'nullable',
            'id' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'fechaInicio' => 'fechaInicio es requerida',
            'fechaFin' => 'fechaFin es requerida',
        ];
    }
}
