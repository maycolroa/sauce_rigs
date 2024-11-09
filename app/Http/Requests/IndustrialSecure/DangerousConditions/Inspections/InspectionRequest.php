<?php

namespace App\Http\Requests\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\LocationFormTrait;
use Session;

class InspectionRequest extends FormRequest
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
        if ($this->has('themes'))
        {
            foreach ($this->input('themes') as $key => $value)
            {
                $data['themes'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('additional_fields'))
        {
            foreach ($this->input('additional_fields') as $key => $value)
            {
                $data['additional_fields'][$key] = json_decode($value, true);
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
        $id = $this->input('id');

        $rules = [
            'name' => 'required|string|unique:sau_ph_inspections,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'type_id' => 'required|integer',
            'description' => 'nullable|min:10|max:1000',
            'fullfilment_parcial' => 'nullable|numeric|max:1',
            'themes' => 'required|array',
            'themes.*.name' => 'required',
            'themes.*.items' => 'required|array',
            'themes.*.items.*.description' => 'required',
            'additional_fields' => 'nullable|array'
        ];

        if ($this->input('type_id')  == 2)
        {
            $rules['themes.*.items.*.compliance_value'] = 'required|numeric|max:100';
            $rules['themes.*.items.*.partial_value'] = 'required|numeric|max:100';
        }

        $confLocation = $this->getLocationFormConfModule();

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
        }

        return $rules;
    }
}
