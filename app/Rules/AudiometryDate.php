<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Carbon\Carbon;

class AudiometryDate implements Rule
{
    protected $id;

    protected $employee_id;

    protected $date_excel;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id, $employee_id, $date_excel = '')
    {
        $this->id = $id;
        $this->employee_id = $employee_id;
        $this->date_excel = $date_excel;
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
        if ($this->employee_id != null && $value != null)
        {
            if ($this->date_excel != '')
                $date = $this->date_excel;
            else
                $date = (Carbon::createFromFormat('D M d Y', $value))->format('Y-m-d');
            
            $audiometry = Audiometry::where('date', $date)->where('employee_id', $this->employee_id)->first();

            if ($audiometry)
            {
                if (!$this->id)
                {
                    return false;
                }
            }
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
        return 'El empleado ya posee una audiometria para el dia seleccionado';
    }
}
