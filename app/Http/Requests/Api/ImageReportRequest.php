<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FileFormat;
use App\Rules\Api\CheckCompany;
use App\Rules\Api\CheckLicense;
use Auth;

class ImageReportRequest extends FormRequest
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
            'report_id' => 'required|numeric',
            'image_1' => ['image', 'max:10000', new FileFormat(['png','jpg','jpeg'])],
            'image_2' => ['image', 'max:10000', new FileFormat(['png','jpg','jpeg'])],
            'image_3' => ['image', 'max:10000', new FileFormat(['png','jpg','jpeg'])]
        ];
    }
}
