<?php

namespace App\Http\Requests\LegalAspects\LegalMatrix;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;

class SaveArticlesQualificationRequest extends FormRequest
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

        if ($this->has('articles'))
        {
            foreach ($this->input('articles') as $key => $value)
            {
                $data['articles'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        /*if ($this->has('risk'))
        {
            foreach ($this->input('risk') as $key => $value)
            {
                $data['risk'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        /*$record = $this->input('risk');*/

        if ($this->has('risk'))
        {
            foreach (json_decode($this->input('risk'), true) as $key => $value)
            {
                $data['risk'][$key] = $value;
                $this->merge($data);
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

        $rulesActionPlan = ActionPlan::prefixIndex('articles.*.')->getRules($this->all());
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
