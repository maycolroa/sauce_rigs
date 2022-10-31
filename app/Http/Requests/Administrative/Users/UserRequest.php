<?php

namespace App\Http\Requests\Administrative\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\General\Team;
use App\Traits\LocationFormTrait;
use Session;

class UserRequest extends FormRequest
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
    public function sanitize()
    {
        if ($this->has('filter_inspections'))
        {
            $this->merge([
                'filter_inspections' => json_decode($this->input('filter_inspections'), true)
            ]);
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
        \Log::info($this);
        $id = $this->input('id');

        $rules = [
            'name'      => 'required|string',
            'email'     => 'required|email|unique:sau_users,email,' . $id . ',id',
            'document'  => 'required|numeric',
            'password'  => ['nullable', 'regex:/^(?=.*\d)(?=.*[@$!%*?&._-])([A-Za-z\d@$!%*?&._-]|[^ ]){8,}$/']
            //'email' => 'regex:/^.+@.+$/i'
        ];

        $confLocation = $this->getLocationFormConfUser(Session::get('company_id'));
        \Log::info($confLocation);

        if ($confLocation)
        {
            if ($confLocation['regional'] == 'SI')
                $rules['employee_regional_id'] = 'required';
            if ($confLocation['headquarter'] == 'SI')
                $rules['employee_headquarter_id'] = 'required';
            if ($confLocation['process'] == 'SI')
                $rules['employee_process_id'] = 'required';
            if ($confLocation['area'] == 'SI')
                $rules['employee_area_id'] = 'required';
        }

        \Log::info($rules);
        $team = Team::where('name', Session::get('company_id'))->first()->id;

        if (!Auth::user()->hasRole('Arrendatario', $team) && !Auth::user()->hasRole('Contratista', $team) && $this->input('edit_role') == 'true')
            $rules['role_id'] = 'required';
        
        return $rules;
    }
}
