<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Reports;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Exception;

class ConditionType extends Model
{
    /**
     * tabla relacionada con el modelo
     * @var string
     */
    protected $table = 'sau_ph_conditions_types';

    /**
     * atributos permitidos en massive assignment
     * @var array
     */
    protected $fillable = [
        'description'
    ];

    /**
     * Conditions que pertenecen a ConditionType
     * @return Condition
     */
    public function conditions()
    {
        return $this->hasMany(Condition::class)->where([['id', '<>', '98'],['id', '<>', '114']]);
    }
}
