<?php

namespace App\Http\Requests\IndustrialSecure\RoadSafety\Vehicles;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\LocationFormTrait;
use Session;

class MaintenanceRequest extends FormRequest
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
        if ($this->has('evidences'))
        {
            foreach ($this->input('evidences') as $key => $value)
            {
                $data['evidences'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('files_binary') && COUNT($this->files_binary) > 0)
        {
            $data = $this->all();

            foreach ($this->files_binary as $key => $value)
            {
                $data['evidences'][$key]['file'] = $value;
            }

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
        $id = $this->input('id');

        $rules = [
            'date' => 'required',
            'type' => 'required',
            'km' => 'required',
            'description' => 'required',
            'responsible' => 'required',
            'apto' => 'required',
            'reason' => 'nullable',
            'next_date' => 'required',
        ];

        return $rules;
    }
}
