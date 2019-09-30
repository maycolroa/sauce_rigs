<?php

namespace App\Models\IndustrialSecure\Inspections;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class Condition extends Model
{
    public $timestamps = false;

    /**
     * tabla relacionada con el modelo
     * @var string
     */
    protected $table='sau_inspect_conditions';

    /**
     * atributos permitidos en massive assignment
     * @var array
     */
    protected $fillable=[
        'description'
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
        return $this->hasMany(ConditionReport::class, 'condition_id');
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
