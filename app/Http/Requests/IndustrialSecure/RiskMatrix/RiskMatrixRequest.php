<?php

namespace App\Http\Requests\IndustrialSecure\RiskMatrix;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\LocationFormTrait;
use Session;


class RiskMatrixRequest extends FormRequest
{
    use LocationFormTrait;

    protected $messages = [];

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
            $data3['locations'] = json_decode($this->get('locations'), true);
            $this->merge($data3);
        }

        if ($this->has('participants') && $this->input('participants'))
        {
            foreach ($this->input('participants') as $key => $value)
            {
                $data['participants'][$key] = json_decode($value, true);
                $this->merge($data);
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

        $rules = [
            'name' => 'nullable|string|unique:sau_rm_risks_matrix,name,'.$id.',id,company_id,'.$this->company,
            'approved' => 'nullable',
            'participants' => 'nullable|array',
        ];

        $confLocation = $this->getLocationFormRules();

        $rules = array_merge($rules, $confLocation);

        return $rules;
    }
}
