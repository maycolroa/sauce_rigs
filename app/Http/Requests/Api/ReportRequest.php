<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\Rules\Api\CheckCompany;
use App\Rules\Api\CheckLicense;
use Constant;
//use App\Traits\LocationFormTrait;

class ReportRequest extends FormRequest
{
    //use LocationFormTrait;

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
        $this->merge(['rate' => ucfirst($this->input('rate'))]);
        
        /*if ($this->has('locations'))
        {
            $this->merge([
                'locations' => json_decode($this->input('locations'), true)
            ]);
        }*/

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rates = implode(',', Constant::getConstant('PH_RATES'));
        \Log::info($this->input('rate'));

         $rules = [
            'company_id' => ['required', 'numeric', new CheckCompany(Auth::guard('api')->user()->id), new CheckLicense()],
            'observation' => 'required',
            'condition_id' => 'required|exists:sau_ph_conditions,id|numeric',
            'rate' => "required|in:$rates",
            'other_condition' => 'string|max:255'
        ];

        /*$rulesConfLocation = $this->getLocationFormRules();
        $rules = array_merge($rules, $rulesConfLocation);*/

        return $rules;
    }

   /* public function messages()
    {
        $keywords = Auth::user()->getKeywords();
        
        return [
            'locations.employee_regional_id.required' => 'El campo '.$keywords['regional'].' es obligatorio.',
            'locations.employee_headquarter_id.required' => 'El campo '.$keywords['headquarter'].' es obligatorio.',
            'locations.employee_process_id.required' => 'El campo '.$keywords['process'].' es obligatorio.',
            'locations.employee_area_id.required' => 'El campo '.$keywords['area'].' es obligatorio.'
        ];
    }*/
}
