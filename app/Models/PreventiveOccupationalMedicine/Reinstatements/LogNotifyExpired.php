<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;

class LogNotifyExpired extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_reinc_notify_incapacitated_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'check_id',
        'cod_dx',
        'email_send',
    ];
}