<?php

namespace App\Rules\Api;

use Illuminate\Contracts\Validation\Rule;
use DB;
use App\Facades\General\PermissionService;
use App\Traits\LocationFormTrait;


class CheckLocationConfiguration implements Rule
{
    use LocationFormTrait;

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
        $location = $this->getLocationFormConfModule($this->company_id);

        $level = "";
        $level2 = "";

        if ($location['area'] == 'SI')
            $level = 4;
        else if ($location['process'] == 'SI')
            $level = 3;
        else if ($location['headquarter'] == 'SI')
            $level = 2;
        else
            $level = 1;

        if ($attribute == 'employee_area_id')
            $level2 = 4;
        else if ($attribute == 'employee_process_id')
            $level2 = 3;
        else if ($attribute == 'employee_headquarter_id')
            $level2 = 2;
        else
            $level2 = 1;

        if ($level2 <= $level)
        {
            if($value)
                return true;
            else
                return false;
        }
        else
            return true;

    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "El campo :attribute es requerido";
    }
}
