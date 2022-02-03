<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;

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
        if ($this->has('files'))
        {
            $this->merge([
                'files' => json_decode($this->input('files'), true)
            ]);
        }

        if ($this->has('items'))
        {
            foreach ($this->input('items') as $key => $value)
            {
                $data['items'][$key] = json_decode($value, true);
                $this->merge($data);

                if ($this->has('files_binary') && COUNT($this->files_binary) > 0)
                {
                    foreach ($this->files_binary as $key2 => $value)
                    {
                        $info = [
                            'id' => isset($this->input('files')[$key2]['id']) ? $this->input('files')[$key2]['id'] : null,
                            'key' => $this->input('files')[$key2]['key'],
                            'name' => $this->input('files')[$key2]['name'],
                            'old_name' => isset($this->input('files')[$key2]['old_name']) ? $this->input('files')[$key2]['old_name'] : null,
                            'expirationDate' => $this->input('files')[$key2]['expirationDate'],
                            'file' => $value
                        ];

                        $data['files'][$key2] = $info;
                        $data2['items'][$key]['files'][$key2] = $info;
                    }

                    $this->merge($data);
                    $this->merge($data2);
                }
            }
        }

        if ($this->has('actionPlan'))
        {
            $this->merge([
                'actionPlan' => json_decode($this->input('actionPlan'), true)
            ]);
        }

        if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
            ]);
        }

        if ($this->has('activities_defined'))
        {
            $this->merge([
                'activities_defined' => json_decode($this->input('activities_defined'), true)
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
            "items.*.files.*.name" => "required_if:qualification,C",
            "items.*.files.*.file" => "required_if:qualification,C|max:20480",
            "items.*.files.*.expirationDate" => "nullable|date"
        ];

        $record = SectionCategoryItems::find($this->input('id'));

        if ($record)
        {
            $record =  $record->itemStandardCompany(Session::get('company_id'))->first();

            if ($record && $record->pivot->required == 'SI')
            {
                $rules = array_merge($rules, [
                    "items.*.files" => "required_if:qualification,C"
                ]);
            }
        }

        $rulesActionPlan = ActionPlan::prefixIndex('items.*.')->getRules($this->all());
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
