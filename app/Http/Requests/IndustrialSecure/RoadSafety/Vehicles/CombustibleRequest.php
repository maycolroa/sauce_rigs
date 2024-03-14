<?php

namespace App\Http\Requests\IndustrialSecure\RoadSafety\Vehicles;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\LocationFormTrait;
use Session;

class CombustibleRequest extends FormRequest
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
            'driver_id' => 'required',
            'km' => 'required',
            'cylinder_capacity' => 'required',
            'quantity_galons' => 'required',
            'price_galon' => 'required',
        ];

        return $rules;
    }
}
