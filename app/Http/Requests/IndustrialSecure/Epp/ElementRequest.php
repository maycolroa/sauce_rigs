<?php

namespace App\Http\Requests\IndustrialSecure\Epp;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class ElementRequest extends FormRequest
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
        if ($this->has('type'))
        {
            foreach ($this->input('type') as $key => $value)
            {
                $data['type'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('mark'))
        {
            foreach ($this->input('mark') as $key => $value)
            {
                $data['mark'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        } 

        if ($this->has('applicable_standard'))
        {
            if (is_array($this->input('applicable_standard')) && count($this->input('applicable_standard')) > 0)
            {
                foreach ($this->input('applicable_standard') as $key => $value)
                {
                    $data['applicable_standard'][$key] = json_decode($value, true);
                    $this->merge($data);
                }
            }
        }

        if ($this->has('locations_stock'))
        {
            foreach ($this->input('locations_stock') as $key => $value)
            {
                $data['locations_stock'][$key] = json_decode($value, true);
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
        $id = $this->input('id');
        //\Log::info($this->all());

        return [
            'name' => 'required|string|unique:sau_epp_elements,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'code' => 'required|string',
            'description' => 'required|string',
            'type' => 'required_if:inventary,SI',
            'mark' => 'required',
            'applicable_standard' => 'nullable|array',
            'observations' => 'nullable|string',
            'operating_instructions' => 'nullable|string',
            'state' => 'required',
            'reusable' => 'required',
            'image' => 'nullable',
            //'identify_each_element' => 'required'
        ];
    }
}
