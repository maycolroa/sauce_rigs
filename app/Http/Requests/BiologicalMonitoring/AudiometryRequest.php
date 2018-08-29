<?php

namespace App\Http\Requests\BiologicalMonitoring;

use Illuminate\Foundation\Http\FormRequest;

class AudiometryRequest extends FormRequest
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
            'date' => 'required|date|before_or_equal:today',
            'type' => 'max:250|nullable',
            'previews_events' => 'nullable',
            'employee_id' => 'required|exists:sau_employees,id',
            'work_zone_noise' => 'max:250|nullable',
            'exposition_level' => 'max:250|nullable',
            'left_500' => 'required|integer',
            'left_1000' => 'required|integer',
            'left_2000' => 'required|integer',
            'left_3000' => 'required|integer',
            'left_4000' => 'required|integer',
            'left_6000' => 'required|integer',
            'left_8000' => 'required|integer',
            'right_500' => 'required|integer',
            'right_1000' => 'required|integer',
            'right_2000' => 'required|integer',
            'right_3000' => 'required|integer',
            'right_4000' => 'required|integer',
            'right_6000' => 'required|integer',
            'right_8000' => 'required|integer',
            'left_clasification' => 'max:250|nullable',
            'right_clasification' => 'max:250|nullable',
            'recommendations' => 'nullable',
            'obs' => 'nullable',
            'test_score' => 'nullable|integer',
            'epp' => 'max:250|nullable',
        ];
    }
}
