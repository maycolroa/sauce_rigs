<?php

namespace App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;

class SaveQualificationRequest extends FormRequest
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

        if ($this->has('themes'))
        {
            foreach ($this->input('themes') as $keyTheme => $value)
            {
                foreach ($value as $keyItem => $activities)
                {
                    $data['themes'][$keyTheme]['items'][$keyItem] = json_decode($activities, true);
                    $this->merge($data);
                }
            }
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
        $rules = [];

        $rulesActionPlan = ActionPlan::prefixIndex('themes.*.items.*.')->getRules($this->all());
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
        $messages = [];

        $messages = array_merge($messages, $this->messages);

        return $messages;
    }
}
