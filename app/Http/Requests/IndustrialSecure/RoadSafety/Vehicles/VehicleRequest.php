<?php

namespace App\Http\Requests\IndustrialSecure\RoadSafety\Vehicles;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\LocationFormTrait;
use Session;

class VehicleRequest extends FormRequest
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
        if ($this->has('plate') && $this->plate)
        {
            foreach ($this->input('plate') as $key => $value)
            {
                $data['plate'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('name_propietary') && $this->name_propietary)
        {
            foreach ($this->input('name_propietary') as $key => $value)
            {
                $data['name_propietary'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('type_vehicle') && $this->type_vehicle)
        {
            foreach ($this->input('type_vehicle') as $key => $value)
            {
                $data['type_vehicle'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('mark') && $this->mark)
        {
            foreach ($this->input('mark') as $key => $value)
            {
                $data['mark'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('line') && $this->line)
        {
            foreach ($this->input('line') as $key => $value)
            {
                $data['line'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('model') && $this->model)
        {
            foreach ($this->input('model') as $key => $value)
            {
                $data['model'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('color') && $this->color)
        {
            foreach ($this->input('color') as $key => $value)
            {
                $data['color'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('loading_capacity') && $this->loading_capacity)
        {
            foreach ($this->input('loading_capacity') as $key => $value)
            {
                $data['loading_capacity'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('locations') && $this->locations)
        {
            $this->merge([
                'locations' => json_decode($this->input('locations'), true)
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
            'plate' => 'required',
            'name_propietary' => 'required',
            'registration_number' => 'required',
            'registration_number_date' => 'required',
            'type_vehicle' => 'required',
            'code_vehicle' => 'nullable',
            'mark' => 'required',
            'line' => 'required',
            'model' => 'required',
            'cylinder_capacity' => 'required',
            'color' => 'required',
            'chassis_number' => 'required',
            'engine_number' => 'required',
            'passenger_capacity' => 'required',
            'loading_capacity' => 'required',
            'state' => 'required',
            'soat_number' => 'required',
            'insurance' => 'required',
            'expedition_date_soat' => 'required',
            'due_date_soat' => 'required',
            'file_soat' => 'nullable',
            'mechanical_tech_number' => 'required',
            'issuing_entity' => 'required',
            'expedition_date_mechanical_tech' => 'required',
            'due_date_mechanical_tech' => 'required',
            'file_mechanical_tech' => 'nullable',
            'policy_number' => 'nullable',
            'policy_entity' => 'nullable',
            'expedition_date_policy' => 'nullable',
            'due_date_policy' => 'nullable',
            'file_policy' => 'nullable',
        ];

        $confLocation = $this->getLocationFormConfModule();

        $rulesConfLocation = $this->getLocationFormRules();
        $rules = array_merge($rules, $rulesConfLocation);


        /*if ($confLocation)
        {
            if ($confLocation['regional'] == 'SI')
                $rules['location.employee_regional_id'] = 'required';
            if ($confLocation['headquarter'] == 'SI')
                $rules['location.employee_headquarter_id'] = 'required';
            if ($confLocation['process'] == 'SI')
                $rules['location.employee_process_id'] = 'required';
            if ($confLocation['area'] == 'SI')
                $rules['location.employee_area_id'] = 'required';
        }*/

        return $rules;
    }
}
