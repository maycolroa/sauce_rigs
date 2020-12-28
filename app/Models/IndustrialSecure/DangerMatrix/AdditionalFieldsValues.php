<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class AdditionalFieldsValues extends Model
{    
    public $table = 'sau_dm_add_fields_values';

    protected $fillable = [
        'field_id',
        'value',
        'danger_matrix_id'
    ];
    
    public function fields()
    {
        return $this->belongsTo(AdditionalFields::class);
    }

    public function matrix()
    {
        return $this->belongsTo(DangerMatrix::class);
    }
}