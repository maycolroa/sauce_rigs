<?php

namespace App\Http\Requests\Administrative\Configuration;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class ConfigurationRequest extends FormRequest
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
            'location_level_form' => 'nullable',
            'days_alert_expiration_date_action_plan' => 'nullable|min:0',
            'expired_elements_asigned' => 'nullable',
            'days_alert_expiration_date_elements' => 'required_if:expired_elements_asigned,SI',
            'filter_inspections' => 'nullable',
            'location_level_form_user_inspection_filter' => 'required_if:filter_inspections,SI',
        ];
    }
}
