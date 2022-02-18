<?php

namespace App\Http\Requests\IndustrialSecure\Epp;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class ElementReceptionRequest extends FormRequest
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
        if ($this->has('elements_id'))
        {
            foreach ($this->input('elements_id') as $key => $value)
            {
                $data['elements_id'][$key] = json_decode($value, true);
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
        //\Log::info($this->all());

        return [
            'location_origin_id' => 'required',
            'location_destiny_id' => 'required',
            'elements_id' => 'required|array',
            'elements_id.*.reception' => 'required',
            'elements_id.*.quantity_complete' => 'required',
            'elements_id.*.quantity_reception' => 'integer|required_if:elements_id.*.type,No Identificable',
            'elements_id.*.codes_reception' => 'array|required_if:elements_id.*.type,Identificable',
            'elements_id.*.type' => 'string',
            'state' => 'required',
        ];
    }
}
