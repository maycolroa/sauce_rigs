<?php

namespace App\Http\Requests\IndustrialSecure\Inspections;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;

use Session;

class ConditionReportRequest extends FormRequest
{
    protected $messages = [];

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
            /*"items.*.files.*.name" => "required_if:items.*.qualification,C",
            "items.*.files.*.file" => "required_if:items.*.qualification,C|max:20480",
            "items.*.files.*.expirationDate" => "nullable|date"*/
        ];

        $rulesActionPlan = ActionPlan::getRules($this->all());
        $rules = array_merge($rules, $rulesActionPlan['rules']);
        $this->messages = $rulesActionPlan['messages'];

        return $rules;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function messages()
    {
        $messages = [
            /*'items.*.files.*.name.required_if' => 'Requerido',
            'items.*.files.*.file.required_if' => 'Requerido',
            'items.*.files.*.expirationDate' => 'Fecha de vencimiento debe ser una fecha'*/
        ];

        $messages = array_merge($messages, $this->messages);

        return $messages;
    }
}
