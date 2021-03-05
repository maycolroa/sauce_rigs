<?php

namespace App\Models\System\LogMails;

use Illuminate\Database\Eloquent\Model;

class LogMail extends Model
{
    protected $table = 'sau_log_mails';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'module_id',
        'event',
        'recipients',
        'copyHidden',
        'subject',
        'message',
        'created_at',
        'message_id'
    ];

    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo('App\Models\General\Module');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\General\Company');
    }

    /**
     * filters checks through the given companies
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $companies
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInCompanies($query, $companies, $typeSearch = 'IN')
    {
        if (COUNT($companies) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_log_mails.company_id', $companies);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_log_mails.company_id', $companies);
        }

        return $query;
    }

    /**
     * filters checks through the given modules
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $modules
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInModules($query, $modules, $typeSearch = 'IN')
    {
        if (COUNT($modules) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_log_mails.module_id', $modules);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_log_mails.module_id', $modules);
        }

        return $query;
    }
}
