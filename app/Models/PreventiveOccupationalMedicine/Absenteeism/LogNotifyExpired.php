<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Database\Eloquent\Model;

class LogNotifyExpired extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_absen_notify_expired_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'document',
        'cod_dx',
        'email_send',
    ];
}