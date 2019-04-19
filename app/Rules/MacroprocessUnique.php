<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Administrative\Regionals\EmployeeRegional;

class MacroprocessUnique implements Rule
{
    protected $id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
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
            $macroprocess = EmployeeRegional::
                  join('sau_employees_headquarters', 'sau_employees_headquarters.employee_regional_id', 'sau_employees_regionals.id')
                ->join('sau_headquarter_process', 'sau_headquarter_process.employee_headquarter_id', 'sau_employees_headquarters.id')
                ->join('sau_employees_processes', 'sau_employees_processes.id', 'sau_headquarter_process.employee_process_id')
                ->where('sau_employees_processes.id', '!=', $this->id)
                ->where('sau_employees_processes.name', $value)
                ->first();

            if ($macroprocess)
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
