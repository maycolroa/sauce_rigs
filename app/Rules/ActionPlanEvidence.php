<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ActionPlanEvidence implements Rule
{
    protected $params;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
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
        $pos = strpos($attribute, "activities.");
        $pos = substr($attribute, $pos + 11);
        $index = str_replace(".evidence_files", "", $pos);

        if ($this->params['actionPlan']['activities'][$index]['evidence'] == 'SI' && 
            $this->params['actionPlan']['activities'][$index]['state'] == 'Ejecutada'
        )
        {
            if (!isset($this->params['actionPlan']['activities'][$index]['evidence_files']) || 
                COUNT($this->params['actionPlan']['activities'][$index]['evidence_files']) == 0
            )
                return false;
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
        return "Malo";
    }
}
