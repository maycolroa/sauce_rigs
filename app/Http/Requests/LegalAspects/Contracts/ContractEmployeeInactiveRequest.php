<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\General\Team;
use Session;
use App\Models\Administrative\Configurations\ConfigurationCompany;

class ContractEmployeeInactiveRequest extends FormRequest
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
        $configuration = ConfigurationCompany::select('value')->where('key', 'contract_validate_inactive_employee');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if ($configuration && $configuration->value == 'SI' && $this->state_employee)
        {
            $rules = [
                "deadline" => "required|date",
                "file_inactivation" => "required",
                "motive_inactivation" => "required"
            ];
        }
        else
        {
            if ($this->state_employee)
            {
                $rules = [
                    "deadline" => "required||date",
                    "file_inactivation" => "nullable|max:20480",
                    "motive_inactivation" => "nullable"
                ];
            }
            else
            {
                $rules = [
                    "deadline" => "nullable||date",
                    "file_inactivation" => "nullable|max:20480",
                    "motive_inactivation" => "nullable"
                ];
            }
        }

        return $rules;
    }
}
