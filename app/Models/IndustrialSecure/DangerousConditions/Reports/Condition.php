<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class Condition extends Model
{
    protected $table='sau_ph_conditions';
   
    protected $fillable=[
        'description',
        'condition_type_id'
    ];

    /**
     * condition type al que pertenece la condición
     * @return ConditionType
     */
    public function conditionType()
    {
        return $this->belongsTo(ConditionType::class);
    }

    /**
     * reporte al que pertenece condition
     * @return Report
     */
    public function reports()
    {
        return $this->hasMany(Report::class, 'condition_id');
    }

    public function multiselect()
    {
        return [
            'name' => $this->description,
            'value' => $this->id
        ];
    }

    /*public function saveFromExcel($row)
    {
        $this->description = str_replace('"', '', $row['descripcion']);
        $this->condition_type_id = ConditionType::select('id')->where('description',$row['tipo'])->first()->id;
        return $this->save();
    }*/
}
