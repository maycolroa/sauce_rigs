<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\Configuration;

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
      $NUMBERS_AVAILABLE_RESULTS = "0,5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,105,110,115,120";
      return [
            'date' => 'required|date|before_or_equal:today',
            'type' => 'max:250|nullable',
            'previews_events' => 'nullable',
            'employee_id' => 'required|exists:sau_employees,id',
            'work_zone_noise' => 'max:250|nullable',
            'exposition_level' => 'max:250|nullable',
            'air_left_500' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_left_1000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_left_2000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_left_3000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_left_4000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_left_6000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_left_8000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_500' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_1000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_2000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_3000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_4000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_6000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'air_right_8000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_left_500' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_left_1000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_left_2000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_left_3000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_left_4000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_right_500' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_right_1000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_right_2000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_right_3000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'osseous_right_4000' => "required|in:$NUMBERS_AVAILABLE_RESULTS|integer|digits_between:0,120",
            'left_clasification' => 'max:250|nullable',
            'right_clasification' => 'max:250|nullable',
            'recommendations' => 'nullable',
            'observation' => 'nullable',
            'test_score' => 'nullable|integer',
        ];
    }
}
