<?php

namespace App\Http\Requests\Administrative\Areas;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\AreaUnique;
use Illuminate\Support\Facades\Auth;

class AreaRequest extends FormRequest
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
            'name' => ['required','string',new AreaUnique($id)],
            'employee_regional_id' => 'required|exists:sau_employees_regionals,id',
            'employee_headquarter_id' => 'required|array',//exists:sau_employees_headquarters,id',
            'employee_process_id' => 'required|array'
        ];
    }

    public function messages()
    {
        $keywords = Auth::user()->getKeywords();
        
        return [
            'employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.',
            'employee_headquarter_id.required' => 'El campo '.$keywords['headquarter'].' es obligatorio.',
            'employee_process_id.required' => 'El campo '.$keywords['process'].' es obligatorio.',
        ];
    }
}
