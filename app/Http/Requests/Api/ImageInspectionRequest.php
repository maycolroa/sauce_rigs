<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FileFormat;
use App\Rules\Api\CheckCompany;
use App\Rules\Api\CheckLicense;
use App\Rules\Api\CheckLocationConfiguration;
use Auth;

class ImageInspectionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => ['required', 'numeric', new CheckCompany(Auth::guard('api')->user()->id), new CheckLicense()],
            'item_id' => 'required|numeric',
            'photo_1' => ['image', 'max:10000', new FileFormat(['png','jpg','jpeg'])],
            'photo_2' => ['image', 'max:10000', new FileFormat(['png','jpg','jpeg'])],
            'key' => 'required',
            'employee_regional_id' => [new CheckLocationConfiguration($this->input('company_id'))],
            'employee_headquarter_id' => [new CheckLocationConfiguration($this->input('company_id'))],
            'employee_process_id' => [new CheckLocationConfiguration($this->input('company_id'))],
            'employee_area_id' => [new CheckLocationConfiguration($this->input('company_id'))]
        ];
    }
}
