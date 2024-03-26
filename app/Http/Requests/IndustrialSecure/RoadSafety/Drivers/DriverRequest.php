<?php

namespace App\Http\Requests\IndustrialSecure\RoadSafety\Drivers;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class DriverRequest extends FormRequest
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
        if ($this->has('documents'))
        {
            foreach ($this->input('documents') as $key => $value)
            {
                $data['documents'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('vehicle_id'))
        {
            foreach ($this->input('vehicle_id') as $key => $value)
            {
                $data['vehicle_id'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('files_binary') && COUNT($this->files_binary) > 0)
        {
            $data = $this->all();

            foreach ($this->files_binary as $key => $value)
            {
                $data['documents'][$key]['file'] = $value;
            }

            $this->merge($data);
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

        return [
            'employee_id' => 'required',
            'documents' => 'nullable|array',
            'responsible_id' => 'required',
            'type_license_id' => 'required',
            'date_license' => 'required'
        ];
    }
}
