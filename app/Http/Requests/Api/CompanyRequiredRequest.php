<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use App\Rules\Api\CheckCompany;
use App\Rules\Api\CheckLicense;

class CompanyRequiredRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => ['required', 'numeric', new CheckCompany(Auth::guard('api')->user()->id), new CheckLicense()]
        ];
    }
}
