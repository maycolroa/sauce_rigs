<?php

namespace App\Http\Requests\IndustrialSecure\RiskMatrix;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class RiskRequest extends FormRequest
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
        if ($this->has('category'))
        {
            if (is_array($this->input('category')))
            {
                foreach ($this->input('category') as $key => $value)
                {
                    $data['category'][$key] = json_decode($value, true);
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
            'name' => 'required|string|unique:sau_rm_risk,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'category' => 'required'
        ];
    }
}
