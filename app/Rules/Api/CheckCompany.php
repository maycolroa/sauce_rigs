<?php

namespace App\Rules\Api;

use Illuminate\Contracts\Validation\Rule;
use DB;

class CheckCompany implements Rule
{
    /**
     * defines if check has indefinite recommendations
     * @var string
     */
    protected $user_id;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
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
        if ($value)
        {
            return DB::table('sau_company_user')->where('user_id', $this->user_id)->where('company_id', $value)->exists();
        }
        else
            return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "El usuario no pertenece a la compañia indicada";
    }
}
