<?php

namespace App\Http\Requests\Administrative\Configurations;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class LocationLevelFormRequest extends FormRequest
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
            'module_id' => 'required|unique:sau_conf_location_level_forms,module_id,'.$id.',id,company_id,'.Session::get('company_id'),
            'location_level_form' => 'required'
        ];
    }
}
