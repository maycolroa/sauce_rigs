<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class Cie10Code extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_cie10_codes';

    public function multiselect()
    {
        return [
            'name' => $this->code.'-'.$this->description,
            'value' => $this->id
        ];
    }
}
