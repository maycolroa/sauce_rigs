<?php

namespace App\Http\Requests\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\LocationFormTrait;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Rules\FileFormat;

class ReportRequest extends FormRequest
{
    use LocationFormTrait;

    protected $message = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        if ($this->has('locations'))
        {
            $this->merge([
                'locations' => json_decode($this->input('locations'), true)
            ]);
        }

        if ($this->has('actionPlan'))
        {
            $this->merge([
                'actionPlan' => json_decode($this->input('actionPlan'), true)
            ]);
        }

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'condition_id' => 'required|exists:sau_ph_conditions,id',
            'rate' => 'required',
            //'observation' => 'required',
            'image_1' => [new FileFormat(['png','jpg','jpeg'])],
            'image_2' => [new FileFormat(['png','jpg','jpeg'])],
            'image_3' => [new FileFormat(['png','jpg','jpeg'])]
        ];

        $rulesActionPlan = ActionPlan::getRules($this->all());
        $rules = array_merge($rules, $rulesActionPlan['rules']);
        $this->message = $rulesActionPlan['messages'];

        $rulesConfLocation = $this->getLocationFormRules();
        $rules = array_merge($rules, $rulesConfLocation);

        return $rules;
    }

    public function messages()
    {
        $keywords = Auth::user()->getKeywords();
        
        $messages = [
            'locations.employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.',
            'locations.employee_headquarter_id.required' => 'El campo '.$keywords['headquarter'].' es obligatorio.',
            'locations.employee_process_id.required' => 'El campo '.$keywords['process'].' es obligatorio.',
            'locations.employee_area_id.required' => 'El campo '.$keywords['area'].' es obligatorio.'
        ];

        $messages = array_merge($messages, $this->message);

        return $messages;
    }
}
