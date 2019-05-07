<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;

use Session;

class ListCheckItemsRequest extends FormRequest
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
        if ($this->has('items'))
        {
            foreach ($this->input('items') as $key => $value)
            {
                $data['items'][$key] = json_decode($value, true);

                if (isset($data['items'][$key]['filesIndex']))
                {
                    $filesIndex = $data['items'][$key]['filesIndex'];

                    if ($this->has($filesIndex))
                    {
                        foreach ($this->$filesIndex as $index => $file)
                        {
                            $data['items'][$key]['files'][$index]['file'] = $file;
                        }
                    }
                }

                $this->merge($data);
            }
        }

        if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
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
            "items.*.files.*.name" => "required_if:items.*.qualification,C",
            "items.*.files.*.file" => "required_if:items.*.qualification,C|max:20480",
            "items.*.files.*.expirationDate" => "nullable|date"
        ];

        $rulesActionPlan = ActionPlan::prefixIndex('items.*.')->getRules();
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
            'items.*.files.*.name.required_if' => 'Requerido',
            'items.*.files.*.file.required_if' => 'Requerido',
            'items.*.files.*.expirationDate' => 'Fecha de vencimiento debe ser una fecha'
        ];

        $messages = array_merge($messages, $this->messages);

        return $messages;
    }
}
