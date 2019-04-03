<?php

namespace App\Http\Requests\Administrative\ActionPlans;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;

class ActionPlanRequest extends FormRequest
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
        $rulesActionPlan = ActionPlan::getRules();
        return $rulesActionPlan['rules'];
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function messages()
    {
        $rulesActionPlan = ActionPlan::getRules();
        return $rulesActionPlan['messages'];
    }
}
