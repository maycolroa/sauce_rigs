<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\Rules\Api\CheckCompany;
use App\Rules\Api\CheckLicense;
use App\Rules\Api\CheckLocationConfiguration;
use Constant;
//use App\Traits\LocationFormTrait;

class InspectionsCreateRequest extends FormRequest
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

    /*public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        //$this->merge(['rate' => ucfirst($this->input('rate'))]);
        
        return $this->all();
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
         $rules = [
            'company_id' => ['required', 'numeric', new CheckCompany(Auth::guard('api')->user()->id), new CheckLicense()],
            'inspection_id' => 'required|numeric'
        ];

        return $rules;
    }
}
