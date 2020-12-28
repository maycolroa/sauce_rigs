<?php

namespace App\Http\Requests\IndustrialSecure\DangerMatrix;
;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class AddFieldsRequest extends FormRequest
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
        if ($this->has('fields'))
        {
            foreach ($this->input('fields') as $key => $value)
            {
                $data['fields'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        /*if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
            ]);
        }*/

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->input('id');

        $rules = [
            'fields.*.name' => 'required',
        ];
        
        return $rules;
    }
}
