<?php

namespace App\Http\Requests\Administrative\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\General\Team;
use App\Traits\LocationFormTrait;
use Session;
use App\Models\Administrative\Roles\Role;
use App\Models\General\Module;
use App\Traits\PermissionTrait;

class UserRequest extends FormRequest
{
    use LocationFormTrait, PermissionTrait;
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

        $roles = [];

        $filters = $this->filtersConfig();

        $validar_rol = false;

        foreach ($filters as $key => $value) {
            if ($value['dangerousConditions'] == 'SI')
                array_push($roles, $key);
        }

        foreach ($this->role_id as $key => $value) {
            $value = json_decode($value);
            \Log::info($value->value);
            if (in_array($value->value, $roles))
            {\Log::info(2);
                $validar_rol = true;
                break;
            }
        }

        if ($validar_rol)
        {
            \Log::info(3);
            $confLocation = $this->getLocationFormConfUser(Session::get('company_id'));

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
        }

        $team = Team::where('name', Session::get('company_id'))->first()->id;

        if (!Auth::user()->hasRole('Arrendatario', $team) && !Auth::user()->hasRole('Contratista', $team) && $this->input('edit_role') == 'true')
            $rules['role_id'] = 'required';
        
        return $rules;
    }

    public function filtersConfig()
    {
        //$includeSuper = $this->user->hasRole('Superadmin', $this->company) ? true : false;

        $roles = Role::alls(true)->get();
        $modules = Module::whereIn('name', ['legalMatrix', 'reinstatements', 'dangerousConditions'])->get();
        $mods_license = [];
        $data = [];

        foreach ($modules as $module)
        {
            $mods_license[$module->id] = $this->checkLicense(Session::get('company_id'), $module->id);
        }

        foreach ($roles as $role)
        {
            $mods = [];

            foreach ($modules as $module)
            {
                $result = 'NO';

                if ($mods_license[$module->id])
                {
                    if ($this->checkRolePermissionInModule($role->id, $module->id))
                    {
                        $result = 'SI';
                    }
                }

                $mods[$module->name] = $result;
            }

            $data[$role->id] = $mods;
        }

        return $data;
    }
}
