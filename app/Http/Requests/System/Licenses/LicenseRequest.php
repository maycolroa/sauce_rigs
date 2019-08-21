<?php

namespace App\Http\Requests\System\Licenses;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class LicenseRequest extends FormRequest
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
            'company_id' => 'required',
            'started_at' => 'required|date|before_or_equal:ended_at',
            'ended_at' => 'required|date',
            'module_id' => 'required|array'
        ];
    }
}
