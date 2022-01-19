<?php

namespace App\Http\Requests\IndustrialSecure\Epp;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class ElementIncomeRequest extends FormRequest
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
            'elements_id' => 'required|array',
            'elements_id.*.quantity' => 'integer|required_if:elements_id.*.type,No Identificable',
            'elements_id.*.codes' => 'array|required_if:elements_id.*.type,Identificable',
            'elements_id.*.type' => 'string',
            'reason' => 'nullable|array',
        ];
    }
}
