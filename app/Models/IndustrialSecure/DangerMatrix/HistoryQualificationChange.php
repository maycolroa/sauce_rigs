<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;

class HistoryQualificationChange extends Model
{
    protected $table = 'sau_dm_history_qualification_change';

    public $timestamps = true;

    protected $fillable = [
        'company_id',
        'user_id',
        'danger_matrix_id',
        'activity_id',
        'danger_id',
        'activity_danger_id',
        'qualification_old',
        'qualification_new',
    ];

    public function danger()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Dangers\Danger', 'danger_id');
    }

    /**
     * filters checks through the given dangers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dangers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInDangers($query, $dangers, $typeSearch = 'IN')
    {
        if (COUNT($dangers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_dm_history_qualification_change.danger_id', $dangers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_dm_history_qualification_change.danger_id', $dangers);
        }

        return $query;
    }
}
