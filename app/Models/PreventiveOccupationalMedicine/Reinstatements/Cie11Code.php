<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class Cie11Code extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_cie11_codes';
    
    protected $fillable = ['code', 'description'];

    public function multiselect()
    {
        return [
            'name' => $this->code.'-'.$this->description,
            'value' => $this->id
        ];
    }
}
