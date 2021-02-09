<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;

class LicenseUsersCompanies implements Rule
{
    protected $company_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        \Log::info("ddd");
        if ($this->company_id)
        {
            \Log::info("ddddddd22");
            $company = Company::find($this->company_id);
            $role = Role::defined()->where('name', 'Superadmin')->first();

            $users = User::withoutGlobalScopes()->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
            ->leftJoin('sau_role_user', function($q) use ($company) { 
                $q->on('sau_role_user.user_id', '=', 'sau_users.id')
                  ->on('sau_role_user.team_id', '=', DB::raw($company->id));
            })
            ->where('sau_role_user.role_id', '<>', $role->id)
            ->groupBy('sau_users.id')
            ->get();

            if ($users->count() == 0)
                return false;
            else
                return true;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Por favor, agregue emails para notificar la creación de la licencia.';
    }
}
