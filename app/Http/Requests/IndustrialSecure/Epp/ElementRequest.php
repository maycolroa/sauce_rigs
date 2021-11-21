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
        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //\Log::info($this->all());

        return [
            'name' => 'required|string',
            'code' => 'required|string',
            'description' => 'required|string',
            'type' => 'required',
            'mark' => 'required',
            'applicable_standard' => 'nullable|string',
            'observations' => 'nullable|string',
            'operating_instructions' => 'nullable|string',
            'state' => 'required',
            'reusable' => 'required',
            'image' => 'nullable'
        ];
    }
}
