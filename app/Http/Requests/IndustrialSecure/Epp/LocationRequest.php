<?php

namespace App\Http\Requests\IndustrialSecure\Epp;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\LocationFormTrait;
use Session;

class LocationRequest extends FormRequest
{
    use LocationFormTrait;
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
        if ($this->has('locations'))
        {
            $data['locations'] = json_decode($this->input('locations'), true);
                $this->merge($data);
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

        $rules = [
            'name'  => 'required|string'
        ];

        $rulesConfLocation = $this->getLocationFormRules();
        $rules = array_merge($rules, $rulesConfLocation);

        /*$confLocation = $this->getLocationFormConfModule();

        if ($confLocation)
        {
            if ($confLocation['regional'] == 'SI')
                $rules['employee_regional_id'] = 'required';
            if ($confLocation['headquarter'] == 'SI')
                $rules['employee_headquarter_id'] = 'required';
            if ($confLocation['process'] == 'SI')
                $rules['employee_process_id'] = 'required';
            if ($confLocation['area'] == 'SI')
                $rules['employee_area_id'] = 'required';
        }*/

        return $rules;
    }
}
