<?php

namespace App\Rules\Reinstatements;

use Illuminate\Contracts\Validation\Rule;

class RequiredIfInProcessIsNo implements Rule
{

    /**
     * defines if the check is in process
     * only possible values must be "SI" or "NO"
     * @var string
     */
    protected $is_in_process;

    /**
     * label for the is_in_process field
     * @var string
     */
    protected $is_in_process_label;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($is_in_process, $is_in_process_label)
    {
        $this->is_in_process = $is_in_process;
        $this->is_in_process_label = $is_in_process_label;
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
        if ($this->is_in_process == 'NO')
        {
            return $value ? true : false;
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
        return "Si actualmente no se encuentra en proceso de {$this->is_in_process_label}, se debe definir si el proceso ya se hizo";
    }
}
