<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Table extends Model
{
    use CompanyTrait;

    protected $table = "sau_absen_tables";

    protected $fillable = [
        'company_id',
        'name',
        'table_name',
        'columns'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'columns' => 'collection'
    ];
}
