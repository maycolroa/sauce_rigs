<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use Session;

class RoleUnique implements Rule
{
    protected $id;

    protected $type_role;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id, $type_role)
    {
        $this->id = $id;
        $this->type_role = $type_role;
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
        if ($value != null)
        {
            if ($this->type_role && $this->type_role == 'Definido')
                $role = Role::withoutGlobalScopes()->where('id', '!=', $this->id)->where('name', $value)->whereNull('company_id')->first();
            else 
               $role = Role::where('id', '!=', $this->id)->where('name', $value)->first();

            if ($role)
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
        return 'Nombre ya ha sido registrado.';
    }
}
