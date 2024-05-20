<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ComplementaryMethodologyLogHistories extends Model
{    
    protected $table = 'sau_dm_complementary_methodology_log_histories';

    protected $fillable = [
        'methodology_id',
        'name_old',
        'name_new',
        'type_old',
        'type_new',
        'observations_old',
        'observations_new',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsTo("App\Models\Administrative\Users\User",'user_id');
    }

    public function methodology()
    {
        return $this->belongsTo(ComplementaryMethodology::class,'user_id');
    }
}
