<?php

namespace App\Http\Requests\LegalAspects\LegalMatrix;

use Illuminate\Foundation\Http\FormRequest;

class LawRiskRequest extends FormRequest
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
        /*$record = (array) $this->input('risk');

        if ($this->has('risk'))
        {
            foreach (json_decode($record[0], true) as $key => $value)
            {
                $data['risk'][$key] = $value;
                $this->merge($data);
            }
        }*/

        if ($this->has('risk'))
        {
            if (is_array($this->input('risk')))
            {
                foreach ($this->input('risk') as $key => $value)
                {
                    $data['risk'][$key] = json_decode($value, true);
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
        $id = $this->input('id');

        return [
            'risk' => 'nullable',//|unique:sau_lm_laws,name,'.$id.',id',
            'risk_oport_description' => 'nullable',
            'type' => 'nullable',
        ];
    }
}
