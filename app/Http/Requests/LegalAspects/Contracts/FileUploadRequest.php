<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\General\Team;
use Session;

class FileUploadRequest extends FormRequest
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
        $rules = [
            "name" => "required",
            "file" => "required|max:20480",
            "expirationDate" => "nullable|date"//|after_or_equal:today"
        ];

        $team = Team::where('name', Session::get('company_id'))->first()->id;

        if (!Auth::user()->hasRole('Arrendatario', $team) && !Auth::user()->hasRole('Contratista', $team))
        {
            $rules['state'] = "required";
            $rules['reason_rejection'] = 'required_if:state,RECHAZADO';
            $rules['contract_id'] = 'required|array';
        }

        return $rules;
    }
}
